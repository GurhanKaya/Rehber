<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\User;
use App\Models\AppointmentSlot;
use Livewire\WithPagination;

class AppointmentManager extends Component
{
    use WithPagination;

    public $layout = 'layouts.admin';
    
    public $showModal = false;
    public $editId = null;
    
    // Appointment form fields
    public $user_id;
    public $appointment_slot_id;
    public $name;
    public $phone;
    public $email;
    public $date;
    public $start_time;
    public $end_time;
    public $status = 'bekliyor';

    // Filtering and search
    public $search = '';
    public $statusFilter = '';
    public $userFilter = '';
    public $dateFilter = '';
    public $appliedSearch = '';
    public $hasSearched = false;
    public $showFilters = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        // Initialize component (properties will be loaded in render method)
    }

    public function searchAppointments()
    {
        $this->appliedSearch = $this->search;
        $this->hasSearched = true;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->userFilter = '';
        $this->dateFilter = '';
        $this->appliedSearch = '';
        $this->hasSearched = false;
        $this->resetPage();
    }

    public function updated($property)
    {
        if (in_array($property, ['statusFilter', 'userFilter', 'dateFilter'])) {
            $this->resetPage();
        }
        
        // Search alanında otomatik arama yapmayın, sadece form submit ile
        if ($property === 'search') {
            return;
        }
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function showCreateModal()
    {
        $this->reset(['editId', 'user_id', 'appointment_slot_id', 'name', 'phone', 'email', 'date', 'start_time', 'end_time', 'status']);
        $this->status = 'bekliyor';
        $this->showModal = true;
    }

    public function showEditModal($id)
    {
        $appointment = Appointment::with('appointmentSlot')->findOrFail($id);
        $this->editId = $appointment->id;
        $this->user_id = $appointment->user_id;
        $this->appointment_slot_id = $appointment->appointment_slot_id;
        $this->name = $appointment->name;
        $this->phone = $appointment->phone;
        $this->email = $appointment->email;
        $this->date = $appointment->date;
        $this->start_time = $appointment->start_time;
        $this->end_time = $appointment->end_time;
        $this->status = $appointment->status;

        $this->showModal = true;
    }

    public function saveAppointment()
    {
        $validated = $this->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required|in:bekliyor,onaylandı,ret,yapıldı',
        ]);

        try {
            if ($this->editId) {
                $appointment = Appointment::findOrFail($this->editId);
                $appointment->update($validated);
                session()->flash('success', 'Randevu başarıyla güncellendi.');
            } else {
                Appointment::create($validated);
                session()->flash('success', 'Randevu başarıyla oluşturuldu.');
            }

            $this->showModal = false;
            $this->reset(['editId', 'user_id', 'appointment_slot_id', 'name', 'phone', 'email', 'date', 'start_time', 'end_time', 'status']);
        } catch (\Exception $e) {
            session()->flash('error', 'Randevu kaydedilirken bir hata oluştu.');
        }
    }

    public function deleteAppointment($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->delete();
            session()->flash('success', 'Randevu başarıyla silindi.');
            $this->showModal = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Randevu silinirken bir hata oluştu.');
        }
    }

    public function updateStatus($id, $status)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->update(['status' => $status]);
            session()->flash('success', 'Randevu durumu güncellendi.');
        } catch (\Exception $e) {
            session()->flash('error', 'Durum güncellenirken bir hata oluştu.');
        }
    }

    public function getAppointments()
    {
        $query = Appointment::with(['user:id,name,surname', 'appointmentSlot'])
            ->select(['id', 'user_id', 'appointment_slot_id', 'name', 'phone', 'email', 'date', 'start_time', 'end_time', 'status', 'created_at']);

        // Apply search filters
        if ($this->hasSearched && $this->appliedSearch !== '') {
            $search = $this->appliedSearch;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('surname', 'like', "%{$search}%");
                  });
            });
        }

        // Apply filters
        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }
        if ($this->userFilter !== '') {
            $query->where('user_id', $this->userFilter);
        }
        if ($this->dateFilter !== '') {
            switch ($this->dateFilter) {
                case 'today':
                    $query->whereDate('date', now()->toDateString());
                    break;
                case 'tomorrow':
                    $query->whereDate('date', now()->addDay()->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
            }
        }

        return $query->orderByDesc('created_at')->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.appointment-manager', [
            'appointments' => $this->getAppointments(),
            'users' => User::where('role', 'personel')->orderBy('name')->get(),
        ])->layout('layouts.admin');
    }
}
