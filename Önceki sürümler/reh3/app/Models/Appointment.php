<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model //Randevu tablosu
{
    protected $fillable = [
        'user_id',
        'appointment_slot_id',
        'name',
        'phone',
        'email',
        'date',
        'status',
        'start_time',
        'end_time',
    ];

    public function appointmentSlot()
    {
        return $this->belongsTo(AppointmentSlot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
