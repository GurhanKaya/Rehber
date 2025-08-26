<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\User;
use App\Models\AppointmentSlot;

class AppointmentEdit extends Component
{
    public $layout = 'layouts.admin';
    
    public Appointment $appointment;
    
    // Form fields
    public $user_id;
    public $appointment_slot_id;
    public $name;
    public $phone;
    public $email;
    public $date;
    public $start_time;
    public $end_time;
    public $status = 'bekliyor';

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->user_id = $appointment->user_id;
        $this->appointment_slot_id = $appointment->appointment_slot_id;
        $this->name = $appointment->name;
        $this->phone = $appointment->phone;
        $this->email = $appointment->email;
        $this->date = $appointment->date;
        $this->start_time = $appointment->start_time;
        $this->end_time = $appointment->end_time;
        $this->status = $appointment->status;
    }

    public function updateAppointment()
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
            $this->appointment->update($validated);
            session()->flash('success', __('app.appointment_updated_success'));
            return redirect()->route('admin.appointments');
        } catch (\Exception $e) {
            session()->flash('error', __('app.appointment_update_failed'));
        }
    }

    public function deleteAppointment()
    {
        try {
            $this->appointment->delete();
            session()->flash('success', __('app.appointment_deleted_success'));
            return redirect()->route('admin.appointments');
        } catch (\Exception $e) {
            session()->flash('error', __('app.delete_failed'));
        }
    }

    public function updateStatus($status)
    {
        try {
            $this->appointment->update(['status' => $status]);
            session()->flash('success', __('app.appointment_status_updated'));
            $this->status = $status;
        } catch (\Exception $e) {
            session()->flash('error', __('app.status_update_failed'));
        }
    }

    public function render()
    {
        return view('livewire.admin.appointment-edit', [
            'users' => User::where('role', 'personel')->orderBy('name')->get(),
            'appointmentSlots' => AppointmentSlot::orderBy('day_of_week')->orderBy('start_time')->get(),
        ])->layout('layouts.admin');
    }
} 