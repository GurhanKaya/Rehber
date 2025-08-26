<?php

namespace App\Livewire\Guest;

use Livewire\Component;
use App\Models\User;
use App\Models\Appointment;

class BookAppointment extends Component
{
    public $user;
    public $slots = [];
    public $appointments = [];
    public $selectedSlotId;
    public $name;
    public $phone;
    public $email;
    public $successMessage = null;
    public $errorMessage = null;
    public $intervals = [];
    public $selectedInterval = null; // ['slot_id' => x, 'start' => 'HH:MM', 'end' => 'HH:MM']
    public $selectedDayOfWeek = null;
    public $selectedDate = null;
    public $infoMessage = null;

    public function mount($id)
    {
        $this->user = User::find($id);
        if (!$this->user) abort(404);
        $this->loadSlots();
        
        // Otomatik olarak en uygun günü bul (mesaj gösterme)
        $this->findNextAvailableSlot(false);
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->loadSlots();
    }

    public function autoSelectIfFull()
    {
        $this->infoMessage = null;
        $selectedDate = $this->selectedDate;
        $selectedDayOfWeek = $selectedDate ? \Carbon\Carbon::parse($selectedDate)->dayOfWeek : null;
        $intervals = $this->intervals[$selectedDayOfWeek] ?? [];
        $allFull = count($intervals) > 0 && collect($intervals)->every(fn($i) => $i['conflict']);
        // Otomatik başka güne atlama ve özel mesaj yok, sadece ilk uygun günü bulma mantığına geri dönüldü.
    }

    public function findNextAvailableSlot($showMessage = true)
    {
        if ($showMessage) {
            $this->infoMessage = null;
            $this->errorMessage = null;
        }
        
        $today = now();
        $slotDays = collect($this->slots)->pluck('day_of_week')->unique()->sort()->values();
        
        // Bugünden itibaren 5 gün içinde ara
        for ($i = 0; $i < 5; $i++) {
            $checkDate = $today->copy()->addDays($i);
            
            if ($slotDays->contains($checkDate->dayOfWeek)) {
                // Bu gün için slotları kontrol et
                $appointments = Appointment::where('user_id', $this->user->id)
                    ->where('date', $checkDate->toDateString())
                    ->get();
                
                $hasAvailableSlot = false;
                
                foreach ($this->slots as $slot) {
                    if ($slot->day_of_week != $checkDate->dayOfWeek) continue;
                    
                    $start = strtotime($slot->start_time);
                    $end = strtotime($slot->end_time);
                    
                    for ($t = $start; $t + 1800 <= $end; $t += 1800) {
                        $intervalStart = date('H:i', $t);
                        $intervalEnd = date('H:i', $t + 1800);
                        
                        $conflict = $appointments->contains(fn($a) =>
                            $a->appointment_slot_id == $slot->id &&
                            $a->start_time == $intervalStart &&
                            $a->end_time == $intervalEnd
                        );
                        
                        // Geçmiş saat kontrolü
                        $isPastTime = $checkDate->toDateString() === now()->toDateString() && $intervalStart <= now()->format('H:i');
                        
                        if (!$conflict && !$isPastTime) {
                            $hasAvailableSlot = true;
                            break 2; // Exit both loops
                        }
                    }
                }
                
                if ($hasAvailableSlot) {
                    $this->selectedDate = $checkDate->toDateString();
                    $this->loadSlots();
                    if ($showMessage) {
                        $this->successMessage = __('app.found_next_available_date', ['date' => $checkDate->format('d.m.Y')]);
                    }
                    return;
                }
            }
        }
        
        if ($showMessage) {
            $this->errorMessage = __('app.no_available_times_next_5_days');
        }
    }

    public function loadSlots()
    {
        $this->slots = $this->user->appointmentSlots()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $this->intervals = [];
        $selectedDate = $this->selectedDate;
        $selectedDayOfWeek = $selectedDate ? \Carbon\Carbon::parse($selectedDate)->dayOfWeek : null;

        // Şu anki zaman
        $now = now();
        $currentTime = $now->format('H:i');
        $isToday = $selectedDate === $now->toDateString();

        // Sadece seçili tarihteki randevuları çek
        $appointments = ($selectedDate)
            ? Appointment::where('user_id', $this->user->id)
                ->where('date', $selectedDate)
                ->get()
            : collect();

        foreach ($this->slots as $slot) {
            // Sadece seçili günün slotlarını göster
            if ($selectedDayOfWeek !== null && $slot->day_of_week != $selectedDayOfWeek) continue;

            $start = strtotime($slot->start_time);
            $end = strtotime($slot->end_time);

            for ($t = $start; $t + 1800 <= $end; $t += 1800) { // 1800 = 30dk
                $intervalStart = date('H:i', $t);
                $intervalEnd = date('H:i', $t + 1800);

                // Bu aralıkta, sadece seçili tarihte randevu var mı?
                $conflict = $appointments->contains(fn($a) =>
                    $a->appointment_slot_id == $slot->id &&
                    $a->start_time == $intervalStart &&
                    $a->end_time == $intervalEnd
                );

                // Geçmiş saat kontrolü (sadece bugün için)
                $isPastTime = $isToday && $intervalStart <= $currentTime;

                $this->intervals[$slot->day_of_week][] = [
                    'slot_id' => $slot->id,
                    'start' => $intervalStart,
                    'end' => $intervalEnd,
                    'conflict' => $conflict || $isPastTime,
                    'is_past' => $isPastTime,
                ];
            }
        }
    }

    public function book()
    {
        $this->errorMessage = null;
        $this->successMessage = null;
        $this->infoMessage = null;
        if (!$this->selectedInterval || !$this->name) {
            $this->errorMessage = __('app.please_select_time_and_enter_name');
            return;
        }
        [$slotId, $start, $end] = explode('|', $this->selectedInterval);
        // Çakışma kontrolü (sadece seçili tarihte)
        $exists = Appointment::where('user_id', $this->user->id)
            ->where('appointment_slot_id', $slotId)
            ->where('start_time', $start)
            ->where('end_time', $end)
            ->where('date', $this->selectedDate)
            ->exists();
        if ($exists) {
            $this->errorMessage = __('app.this_time_already_booked');
            $this->autoSelectIfFull();
            return;
        }
        Appointment::create([
            'user_id' => $this->user->id,
            'appointment_slot_id' => $slotId,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'date' => $this->selectedDate,
            'status' => 'bekliyor',
            'start_time' => $start,
            'end_time' => $end,
        ]);
        $this->successMessage = __('app.appointment_booked_successfully');
        $this->loadSlots();
        $this->reset(['selectedInterval', 'name', 'phone', 'email']);
        $this->autoSelectIfFull();
    }

    public function render()
    {
        return view('livewire.guest.book-appointment')->layout('layouts.guest');
    }
}
