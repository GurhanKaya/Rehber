<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskFile extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'task_id', 
        'file_path', 
        'file_name', 
        'file_size', 
        'mime_type', 
        'user_id'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    protected $touches = ['task'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
