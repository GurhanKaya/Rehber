<?php

namespace App\Livewire\Personel;

use Livewire\Component;
use App\Models\Task;
use Livewire\WithPagination;

class PublicTaskList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function assignToMe($taskId)
    {
        $task = Task::findOrFail($taskId);
        if ($task->type === 'cooperative') {
            if (!$task->participants()->where('user_id', auth()->id())->exists()) {
                $task->participants()->attach(auth()->id());
                session()->flash('success', 'Cooperative göreve başarıyla katıldınız.');
            } else {
                session()->flash('error', 'Bu cooperative göreve zaten katıldınız.');
            }
        } elseif ($task->type === 'public' && is_null($task->assigned_user_id)) {
            $task->assigned_user_id = auth()->id();
            $task->save();
            session()->flash('success', 'Görev başarıyla üzerinize atandı.');
        }
    }

    public function render()
    {
        $userId = auth()->id();
        $tasks = Task::with(['assignedUser', 'participants', 'creator:id,name,surname'])
            ->where(function($q) use ($userId) {
                $q->where(function($q2) {
                    $q2->where('type', 'public')->whereNull('assigned_user_id');
                })
                ->orWhere(function($q2) {
                    $q2->where('type', 'cooperative');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(12);
        return view('livewire.personel.public-task-list', [
            'tasks' => $tasks,
            'userId' => $userId,
        ])->layout('layouts.personel');
    }
} 