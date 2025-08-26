<?php

namespace App\Livewire\Admin;

use App\Livewire\Base\BaseListComponent;
use App\Livewire\Traits\WithTaskList;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout as LWLayout;

#[LWLayout('layouts.admin')]
class TaskManager extends BaseListComponent
{
    use WithTaskList;

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
        return 'livewire.admin.task-manager';
    }

    protected function initializeComponent()
    {
        Gate::authorize('manageTasks');
    }

    protected function clearComponentFilters()
    {
        $this->status = '';
        $this->type = '';
    }

    protected function applyFilters($query)
    {
        return $this->applyTaskFilters($query);
    }

    protected function getViewData(): array
    {
        return [
            'tasks' => $this->getResults(),
            'searched' => $this->searched,
        ];
    }
}

