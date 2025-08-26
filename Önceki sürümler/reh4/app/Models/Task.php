<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'assigned_user_id',
        'created_by',
        'status',
        'deadline',
    ];

    protected $casts = [
        'status' => 'string',
        'type' => 'string',
        'deadline' => 'datetime',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function files()
    {
        return $this->hasMany(\App\Models\TaskFile::class);
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\TaskComment::class);
    }

    public function logs()
    {
        return $this->hasMany(\App\Models\TaskLog::class);
    }

    public function participants()
    {
        return $this->belongsToMany(\App\Models\User::class, 'task_user');
    }
}
