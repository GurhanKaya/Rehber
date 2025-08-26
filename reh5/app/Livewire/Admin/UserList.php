<?php

namespace App\Livewire\Admin;

use App\Livewire\Base\BaseListComponent;
use App\Livewire\Traits\WithUserList;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout as LWLayout;

#[LWLayout('layouts.admin')]
class UserList extends BaseListComponent
{
    use WithUserList;

    protected function getModel(): string
    {
        return User::class;
    }

    protected function getSearchFields(): array
    {
        return ['name', 'surname', 'email', 'department.name', 'title.name'];
    }

    protected function getFilterFields(): array
    {
        return ['department_id', 'title_id', 'hasPhone', 'hasEmail'];
    }

    protected function getOrderBy(): array
    {
        return ['name' => 'asc', 'surname' => 'asc'];
    }

    protected function getViewName(): string
    {
        return 'livewire.admin.user-list';
    }

    protected function initializeComponent()
    {
        Gate::authorize('manageUsers');
        $this->loadFilterOptions('personel');
    }

    protected function clearComponentFilters()
    {
        $this->selectedDepartment = '';
        $this->selectedTitle = '';
        $this->hasPhone = false;
        $this->hasEmail = false;
    }

    protected function applyFilters($query)
    {
        return $this->applyUserFilters($query);
    }

    protected function getViewData(): array
    {
        return [
            'users' => $this->getResults(),
            'searched' => $this->searched,
            'departments' => $this->departments,
            'titles' => $this->titles,
        ];
    }
}
