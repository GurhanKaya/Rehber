<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'name',
        'description', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Department'a ait title'lar
     */
    public function titles(): HasMany
    {
        return $this->hasMany(Title::class);
    }

    /**
     * Aktif title'lar
     */
    public function activeTitles(): HasMany
    {
        return $this->hasMany(Title::class)->where('is_active', true);
    }

    /**
     * Department'a ait kullanıcılar
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Sadece aktif departmanları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
