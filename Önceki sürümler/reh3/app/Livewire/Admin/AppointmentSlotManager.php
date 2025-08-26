<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AppointmentSlot;
use App\Models\User;

class AppointmentSlotManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $users;
    public $showModal = false;
    public $editId = null;
    
    public $user_id;
    public $day_of_week;
    public $start_time;
    public $end_time;
    public $is_available = true;

    // Arama filtreleri
    public $search = '';
    public $selectedUser = '';
    public $selectedDay = '';
    public $selectedStatus = '';
    public $showFilters = false;
    public $hasSearched = false;

    public function mount()
    {
        $this->users = User::where('role', 'personel')->orderBy('name')->get();
    }

    public function updatedUserId()
    {
        // Kullanıcı seçildiğinde mevcut saatleri yenile
        $this->dispatch('userSelected');
    }

    public function searchSlots()
    {
        $this->hasSearched = true;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedUser = '';
        $this->selectedDay = '';
        $this->selectedStatus = '';
        $this->hasSearched = false;
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function updated($property)
    {
        if (in_array($property, ['selectedUser', 'selectedDay', 'selectedStatus'])) {
            $this->resetPage();
        }
    }

    public function getExistingSlotsForUserProperty()
    {
        if (!$this->user_id) {
            return collect();
        }

        return AppointmentSlot::where('user_id', $this->user_id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    public function showCreateModal()
    {
        $this->reset(['editId', 'user_id', 'day_of_week', 'start_time', 'end_time', 'is_available']);
        $this->is_available = true;
        $this->showModal = true;
    }

    public function showEditModal($id)
    {
        $slot = AppointmentSlot::findOrFail($id);
        $this->editId = $slot->id;
        $this->user_id = $slot->user_id;
        $this->day_of_week = $slot->day_of_week;
        $this->start_time = $slot->start_time;
        $this->end_time = $slot->end_time;
        $this->is_available = $slot->is_available;
        $this->showModal = true;
    }

    public function saveSlot()
    {
        $validated = $this->validate([
            'user_id' => 'required|exists:users,id',
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required',
            'end_time' => 'required',
            'is_available' => 'boolean',
        ]);

        // Çakışma kontrolü
        $conflictingSlot = $this->checkForConflicts($validated);
        if ($conflictingSlot) {
            $user = User::find($validated['user_id']);
            $days = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
            $dayName = $days[$validated['day_of_week']];
            
            session()->flash('error', '❌ ÇAKIŞMA TESPİT EDİLDİ! Lütfen farklı bir saat aralığı seçin.');
            return;
        }

        try {
            if ($this->editId) {
                $slot = AppointmentSlot::findOrFail($this->editId);
                $slot->update($validated);
                session()->flash('success', 'Randevu saati güncellendi.');
            } else {
                AppointmentSlot::create($validated);
                session()->flash('success', 'Randevu saati oluşturuldu.');
            }

            $this->showModal = false;
            $this->reset(['editId', 'user_id', 'day_of_week', 'start_time', 'end_time', 'is_available']);
        } catch (\Exception $e) {
            session()->flash('error', 'İşlem sırasında bir hata oluştu.');
        }
    }

    private function checkForConflicts($data)
    {
        $existingSlots = AppointmentSlot::where('user_id', $data['user_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('id', '!=', $this->editId)
            ->get();

        foreach ($existingSlots as $slot) {
            if ($this->timeRangesOverlap(
                $data['start_time'], 
                $data['end_time'], 
                $slot->start_time, 
                $slot->end_time
            )) {
                return $slot;
            }
        }

        return null;
    }

    private function timeRangesOverlap($start1, $end1, $start2, $end2)
    {
        return $start1 < $end2 && $start2 < $end1;
    }

    public function deleteSlot($id)
    {
        try {
            $slot = AppointmentSlot::findOrFail($id);
            $slot->delete();
            session()->flash('success', 'Randevu saati silindi.');
        } catch (\Exception $e) {
            session()->flash('error', 'Silme işlemi sırasında bir hata oluştu.');
        }
    }

    public function getSlotsProperty()
    {
        $query = AppointmentSlot::with('user');

        // Arama filtresi
        if ($this->search) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('surname', 'like', '%' . $this->search . '%');
            });
        }

        // Personel filtresi
        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }

        // Gün filtresi
        if ($this->selectedDay !== '') {
            $query->where('day_of_week', $this->selectedDay);
        }

        // Durum filtresi
        if ($this->selectedStatus !== '') {
            $query->where('is_available', $this->selectedStatus === 'active');
        }

        return $query->orderBy('day_of_week')->orderBy('start_time')->get();
    }

    public function render()
    {
        return view('livewire.admin.appointment-slot-manager', [
            'slots' => $this->getSlotsProperty(),
            'hasSearched' => $this->hasSearched,
            'existingSlotsForUser' => $this->getExistingSlotsForUserProperty(),
        ])->layout('layouts.admin');
    }
}
