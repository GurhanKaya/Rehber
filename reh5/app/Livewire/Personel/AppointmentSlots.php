<?php

namespace App\Livewire\Personel;

use Livewire\Component;
use App\Models\AppointmentSlot;
use Illuminate\Support\Facades\Auth;

class AppointmentSlots extends Component
{
    public $slots = [];
    public $day_of_week;
    public $start_time;
    public $end_time;
    public $editingSlotId = null;

    protected $rules = [
        'day_of_week' => 'required|integer|min:0|max:6',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
    ];

    public function mount()
    {
        $this->loadSlots();
    }

    public function loadSlots()
    {
        $this->slots = AppointmentSlot::where('user_id', Auth::id())->orderBy('day_of_week')->orderBy('start_time')->get();
    }

    public function editSlot($slotId)
    {
        $slot = AppointmentSlot::where('user_id', Auth::id())->findOrFail($slotId);
        $this->editingSlotId = $slot->id;
        $this->day_of_week = $slot->day_of_week;
        $this->start_time = $slot->start_time;
        $this->end_time = $slot->end_time;
    }

    public function addSlot()
    {
        $this->validate();
        
        // Check for time conflicts
        if ($this->hasTimeConflict()) {
            return;
        }
        
        if ($this->editingSlotId) {
            $slot = AppointmentSlot::where('user_id', Auth::id())->findOrFail($this->editingSlotId);
            $slot->update([
                'day_of_week' => $this->day_of_week,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
            ]);
            $this->editingSlotId = null;
            session()->flash('success', __('app.slot_updated_success'));
        } else {
            AppointmentSlot::create([
                'user_id' => Auth::id(),
                'day_of_week' => $this->day_of_week,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
            ]);
            session()->flash('success', __('app.slot_created_success'));
        }
        $this->reset(['day_of_week', 'start_time', 'end_time']);
        $this->loadSlots();
    }

    private function hasTimeConflict()
    {
        $query = AppointmentSlot::where('user_id', Auth::id())
            ->where('day_of_week', $this->day_of_week);
        
        // Exclude the current slot being edited
        if ($this->editingSlotId) {
            $query->where('id', '!=', $this->editingSlotId);
        }
        
        $existingSlots = $query->get();
        $conflictingSlots = [];
        
        foreach ($existingSlots as $slot) {
            // Check if the new time range overlaps with existing slots
            // Overlap occurs when:
            // 1. New start time is before existing end time AND new end time is after existing start time
            // 2. Or when the new time range completely contains the existing time range
            if (
                ($this->start_time < $slot->end_time && $this->end_time > $slot->start_time) ||
                ($this->start_time <= $slot->start_time && $this->end_time >= $slot->end_time)
            ) {
                $conflictingSlots[] = $slot;
            }
        }
        
        if (!empty($conflictingSlots)) {
            $conflictMessages = [];
            foreach ($conflictingSlots as $slot) {
                $conflictMessages[] = $slot->start_time . ' - ' . $slot->end_time;
            }
            session()->flash('error', __('app.slot_conflict_error'));
            return true;
        }
        
        return false;
    }

    public function deleteSlot($slotId)
    {
        $slot = AppointmentSlot::where('user_id', Auth::id())->findOrFail($slotId);
        $slot->delete();
        $this->loadSlots();
        session()->flash('success', __('app.slot_deleted_success'));
    }

    public function render()
    {
        return view('livewire.personel.appointment-slots')->layout('layouts.personel');
    }
}
