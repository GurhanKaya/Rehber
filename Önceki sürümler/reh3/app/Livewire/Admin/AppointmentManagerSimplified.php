<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\User;
use App\Livewire\Traits\WithAdminSearch;
use App\Livewire\Traits\WithAdminModal;

class AppointmentManagerSimplified extends BaseAdminComponent
{
    use WithAdminSearch, WithAdminModal;

    // Form fields
    public $user_id;
    public $name;
    public $phone;
    public $email;
    public $date;
    public $start_time;
    public $end_time;
    public $status = 'bekliyor';

    // Filters
    public $statusFilter = '';
    public $userFilter = '';
    public $dateFilter = '';

    protected function getViewName(): string
    {
        return 'livewire.admin.appointment-manager';
    }

    protected function getViewData(): array
    {
        return [
            'appointments' => $this->getAppointments(),
            'users' => User::where('role', 'personel')->orderBy('name')->get(),
        ];
    }

    protected function handlePropertyUpdate($property)
    {
        parent::handlePropertyUpdate($property);
        
        if (in_array($property, ['statusFilter', 'userFilter', 'dateFilter'])) {
            $this->resetPage();
        }
    }

    protected function clearComponentFilters()
    {
        $this->statusFilter = '';
        $this->userFilter = '';
        $this->dateFilter = '';
    }

    protected function resetFields()
    {
        $this->reset([
            'user_id', 'name', 'phone', 'email', 
            'date', 'start_time', 'end_time', 'status'
        ]);
    }

    protected function setDefaultValues()
    {
        $this->status = 'bekliyor';
    }

    protected function loadForEdit($id)
    {
        $appointment = Appointment::with('appointmentSlot')->findOrFail($id);
        
        $this->user_id = $appointment->user_id;
        $this->name = $appointment->name;
        $this->phone = $appointment->phone;
        $this->email = $appointment->email;
        $this->date = $appointment->date;
        $this->start_time = $appointment->start_time;
        $this->end_time = $appointment->end_time;
        $this->status = $appointment->status;
    }

    protected function createRecord()
    {
        Appointment::create($this->getAppointmentData());
    }

    protected function updateRecord()
    {
        $appointment = Appointment::findOrFail($this->editId);
        $appointment->update($this->getAppointmentData());
    }

    protected function deleteRecord($id)
    {
        Appointment::findOrFail($id)->delete();
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required|in:bekliyor,onaylandı,ret,yapıldı',
        ];
    }

    public function updateStatus($id, $status)
    {
        try {
            Appointment::findOrFail($id)->update(['status' => $status]);
            session()->flash('success', 'Randevu durumu güncellendi.');
        } catch (\Exception $e) {
            session()->flash('error', 'Durum güncellenirken bir hata oluştu.');
        }
    }

    public function getAppointments()
    {
        $query = Appointment::with(['user:id,name,surname'])
            ->select(['id', 'user_id', 'name', 'phone', 'email', 'date', 'start_time', 'end_time', 'status', 'created_at']);

        // Apply search
        $query = $this->getSearchQuery($query, ['name', 'phone', 'email', 'user.name', 'user.surname']);

        // Apply filters
        $query = $this->applyFilters($query);

        return $query->orderByDesc('created_at')->paginate(15);
    }

    protected function getCreateSuccessMessage(): string
    {
        return 'Randevu başarıyla oluşturuldu.';
    }

    protected function getUpdateSuccessMessage(): string
    {
        return 'Randevu başarıyla güncellendi.';
    }

    protected function getDeleteSuccessMessage(): string
    {
        return 'Randevu başarıyla silindi.';
    }

    private function getAppointmentData(): array
    {
        return [
            'user_id' => $this->user_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
        ];
    }

    private function applyFilters($query)
    {
        return $query
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->userFilter, fn($q) => $q->where('user_id', $this->userFilter))
            ->when($this->dateFilter, function($q) {
                match($this->dateFilter) {
                    'today' => $q->whereDate('date', now()->toDateString()),
                    'tomorrow' => $q->whereDate('date', now()->addDay()->toDateString()),
                    'this_week' => $q->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]),
                    'this_month' => $q->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]),
                    default => $q
                };
            });
    }
} 