<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Notifications\VerifyEmailTurkish;
use App\Notifications\ResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    'id',
    'name',
    'surname',
    'email',
    'password',
    'title_id',
    'role',
    'locale',
    'department_id',
    'phone',
    'photo',
];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function getSlugAttribute()
    {
        return Str::slug($this->name . '-' . $this->surname);
    }

    /**
     * Kullanıcının randevu slotları
     */
    public function appointmentSlots()
    {
        return $this->hasMany(AppointmentSlot::class, 'user_id');
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailTurkish);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Get the policies for the user.
     */
    public function policies()
    {
        return [
            'viewAdminPanel' => $this->role === 'admin',
            'viewPersonelPanel' => $this->role === 'personel',
            'manageUsers' => $this->role === 'admin',
            'manageTasks' => in_array($this->role, ['admin', 'personel']),
            'manageAppointments' => in_array($this->role, ['admin', 'personel']),
            'downloadFiles' => in_array($this->role, ['admin', 'personel']),
        ];
    }

    /**
     * User'ın ait olduğu department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * User'ın title'ı
     */
    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    /**
     * Department name getter (backward compatibility)
     */
    public function getDepartmentNameAttribute()
    {
        return $this->department?->name;
    }

    /**
     * Title name getter (backward compatibility)
     */
    public function getTitleNameAttribute()
    {
        return $this->title?->name;
    }
}
