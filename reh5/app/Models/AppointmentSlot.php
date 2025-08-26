<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentSlot extends Model //Personelin müsait olduğu zamanlar tablosu
{
    protected $fillable = [
        'user_id',
        'day_of_week',
        'start_time',
        'end_time',
        'date', // slotun belirli bir güne atanabilmesi için
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
