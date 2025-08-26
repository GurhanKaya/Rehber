<?php

namespace App\Livewire\Guest;

use App\Livewire\Base\BaseListComponent;
use App\Livewire\Traits\WithUserList;
use App\Models\User;
use Livewire\Attributes\Layout as LWLayout;

#[LWLayout('layouts.guest')]
class UserSearch extends BaseListComponent
{
    use WithUserList;

    protected function getModel(): string
    {
        return User::class;
    }

    protected function getSearchFields(): array
    {
        return ['name', 'surname', 'title', 'department'];
    }

    protected function getFilterFields(): array
    {
        return ['department', 'title', 'hasPhone', 'hasEmail'];
    }

    protected function getOrderBy(): array
    {
        return ['name' => 'asc', 'surname' => 'asc'];
    }

    protected function getViewName(): string
    {
        return 'livewire.guest.user-search';
    }

    protected function initializeComponent()
    {
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
        $query->where('role', 'personel');
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