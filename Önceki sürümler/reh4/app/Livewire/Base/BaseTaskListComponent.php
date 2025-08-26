<?php

namespace App\Livewire\Base;

use App\Models\Task;

abstract class BaseTaskListComponent extends BaseListComponent
{
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

    protected function getBaseQuery()
    {
        return $this->applyRoleFilter(parent::getBaseQuery());
    }

    protected function applyRoleFilter($query)
    {
        // Override in child components for role-specific filtering
        return $query;
    }

    protected function getResults()
    {
        $query = $this->getBaseQuery();
        $query = $this->applySearch($query, $this->query ?? '');
        $query = $this->applyFilters($query);
        $query = $this->applyOrderBy($query);
        
        return $query->paginate($this->perPage);
    }

    protected function getViewData(): array
    {
        return [
            'tasks' => $this->getResults(),
            'searched' => $this->searched ?? false,
        ];
    }
}
