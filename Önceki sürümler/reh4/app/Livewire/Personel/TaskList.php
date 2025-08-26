<?php

namespace App\Livewire\Personel;

use App\Livewire\Base\BaseListComponent;
use Livewire\Attributes\Layout;
use App\Livewire\Traits\WithTaskList;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

#[Layout('layouts.personel')]
class TaskList extends BaseListComponent
{
    use WithTaskList;

    public bool $onlyDeadlineToday = false;
    public bool $onlyMyTasks = false;

    protected function getModel(): string
    {
        return Task::class;
    }

    protected function getSearchFields(): array
    {
        return ['title', 'description', 'type'];
    }

    protected function getFilterFields(): array
    {
        return ['status', 'type'];
    }

    protected function getOrderBy(): array
    {
        return ['created_at' => 'desc'];
    }

    protected function getViewName(): string
    {
        return 'livewire.personel.task-list';
    }

    // Layout attribute handles layout; removing legacy getLayout

    protected function initializeComponent()
    {
        Gate::authorize('viewPersonelPanel');
    }

    protected function applyFilters($query)
    {
        $userId = Auth::id();
        $query->where(function($q) use ($userId) {
            $q->where('assigned_user_id', $userId)
              ->orWhere('created_by', $userId)
              ->orWhereHas('participants', function($p) use ($userId) {
                  $p->where('user_id', $userId);
              });
        });
        if ($this->onlyDeadlineToday) {
            $query->whereDate('deadline', Carbon::today());
        }
        if ($this->onlyMyTasks) {
            $query->where('assigned_user_id', $userId);
        }
        return $this->applyTaskFilters($query);
    }

    protected function clearComponentFilters()
    {
        $this->status = '';
        $this->type = '';
        $this->onlyDeadlineToday = false;
        $this->onlyMyTasks = false;
    }

    protected function getViewData(): array
    {
        return [
            'tasks' => $this->getResults(),
            'searched' => $this->searched,
        ];
    }

    public function filterDeadlineToday(): void
    {
        $this->onlyDeadlineToday = !$this->onlyDeadlineToday;
        $this->searched = true;
        $this->resetPage();
    }

    public function filterMyTasks(): void
    {
        $this->onlyMyTasks = !$this->onlyMyTasks;
        $this->searched = true;
        $this->resetPage();
    }
} 