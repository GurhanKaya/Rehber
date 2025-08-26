<?php

namespace App\Livewire\Personel;

use App\Livewire\Base\BaseListComponent;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Traits\WithTaskList;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;

#[Layout('layouts.personel')]
class PublicTaskList extends BaseListComponent
{
    use WithTaskList;
    public bool $showPublic = true;
    public bool $showCooperative = true;
    public bool $showFilters = false;

    protected function getModel(): string
    {
        return Task::class;
    }

    protected function getSearchFields(): array
    {
        return ['title', 'description'];
    }

    protected function getFilterFields(): array
    {
        return ['status'];
    }

    protected function getOrderBy(): array
    {
        return ['created_at' => 'desc'];
    }

    protected function getViewName(): string
    {
        return 'livewire.personel.public-task-list';
    }

    // Layout attribute handles layout; removing legacy getLayout

    protected function initializeComponent()
    {
        Gate::authorize('viewPersonelPanel');
    }

    protected function applyFilters($query)
    {
        // If both toggles are off, return no results (security: don't leak all tasks)
        if (!$this->showPublic && !$this->showCooperative) {
            $query->whereRaw('1 = 0');
            return $this->applyTaskFilters($query);
        }

        $query->where(function($q) {
            if ($this->showPublic) {
                $q->orWhere(function($sub) {
                    $sub->where('type', 'public')
                        ->whereNull('assigned_user_id');
                });
            }
            if ($this->showCooperative) {
                $q->orWhere('type', 'cooperative');
            }
        });
        return $this->applyTaskFilters($query);
    }

    protected function clearComponentFilters()
    {
        $this->status = '';
        $this->type = '';
        $this->showPublic = true;
        $this->showCooperative = true;
    }

    protected function getViewData(): array
    {
        return [
            'tasks' => $this->getResults(),
            'searched' => $this->searched,
            'showPublic' => $this->showPublic,
            'showCooperative' => $this->showCooperative,
            'userId' => Auth::id(),
        ];
    }

    public function toggleOpenType(string $type): void
    {
        if ($type === 'public') {
            $this->showPublic = !$this->showPublic;
        } elseif ($type === 'cooperative') {
            $this->showCooperative = !$this->showCooperative;
        }
        $this->resetPage();
    }

    public function assignToMe(int $taskId): void
    {
        $task = Task::with('participants')
            ->where('id', $taskId)
            ->whereIn('type', ['public', 'cooperative'])
            ->first();

        if (!$task) {
            return;
        }

        if ($task->type === 'cooperative') {
            // Cooperative görev: katılımcı olarak ekle, görünür kalmalı
            $task->participants()->syncWithoutDetaching([Auth::id()]);
            
            // Log kaydı ekle
            $task->logs()->create([
                'user_id' => Auth::id(),
                'action' => 'joined_cooperative_task',
                'new_value' => __('app.joined_cooperative_task'),
                'details' => __('app.joined_cooperative_task'),
            ]);
        } else {
            // Public görev: atanan kullanıcıya geçir ve açık listeden düşür
            if (!is_null($task->assigned_user_id)) {
                return; // zaten alınmış
            }
            $task->assigned_user_id = Auth::id();
            $task->save();
            
            // Log kaydı ekle
            $task->logs()->create([
                'user_id' => Auth::id(),
                'action' => 'task_assigned',
                'new_value' => __('app.task_assigned'),
                'details' => __('app.task_assigned'),
            ]);
        }

        $this->resetPage();
    }
} 