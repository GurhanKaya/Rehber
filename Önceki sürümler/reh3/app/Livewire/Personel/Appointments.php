<?php

namespace App\Livewire\Personel;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use Livewire\WithPagination;

class Appointments extends Component
{
    use WithPagination;
    public $query = '';
    public $status = '';
    public $onlyToday = false;
    public $onlyPending = false;
    public $showFilters = false;
    protected $paginationTheme = 'tailwind';

    public function search()
    {
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function filterToday()
    {
        $this->onlyToday = !$this->onlyToday;
        $this->onlyPending = false;
        $this->resetPage();
    }

    public function filterPending()
    {
        $this->onlyPending = !$this->onlyPending;
        $this->onlyToday = false;
        $this->resetPage();
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function getAppointments()
    {
        $query = Appointment::with('appointmentSlot')
            ->where('user_id', Auth::id());
        if ($this->query) {
            $q = $this->query;
            $query->where(function($subQ) use ($q) {
                $subQ->where('name', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%") ;
            });
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }
        if ($this->onlyToday) {
            $query->whereDate('date', now()->toDateString());
        }
        if ($this->onlyPending) {
            $query->where('status', 'bekliyor');
        }
        if (!$this->query && !$this->status && !$this->onlyToday && !$this->onlyPending) {
            $today = now()->toDateString();
            $query->orderByRaw(
                "CASE ".
                "WHEN date = ? AND status = 'onaylandı' THEN 0 ".
                "WHEN status = 'bekliyor' THEN 1 ".
                "ELSE 2 END", [$today]
            );
        }
        return $query
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(12);
    }

    public function approveAppointment($id)
    {
        $appointment = Appointment::where('user_id', Auth::id())->findOrFail($id);
        $appointment->status = 'onaylandı';
        $appointment->save();
    }

    public function rejectAppointment($id)
    {
        $appointment = Appointment::where('user_id', Auth::id())->findOrFail($id);
        $appointment->status = 'ret';
        $appointment->save();
    }

    public function markAsDoneAppointment($id)
    {
        $appointment = Appointment::where('user_id', Auth::id())->findOrFail($id);
        $appointment->status = 'yapıldı';
        $appointment->save();
    }

    public function setPendingAppointment($id)
    {
        $appointment = Appointment::where('user_id', Auth::id())->findOrFail($id);
        $appointment->status = 'bekliyor';
        $appointment->save();
    }

    public function deleteAppointment($id)
    {
        $appointment = Appointment::where('user_id', Auth::id())->findOrFail($id);
        $appointment->delete();
    }

    public function render()
    {
        return view('livewire.personel.appointments', [
            'appointments' => $this->getAppointments(),
        ])->layout('layouts.personel');
    }
}
