<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Livewire\Traits\WithAdminSearch;

class UserListSimplified extends BaseAdminComponent
{
    use WithAdminSearch;

    public string $selectedDepartment = '';
    public string $selectedTitle = '';
    public bool $hasPhone = false;
    public bool $hasEmail = false;
    public string $viewMode = 'grid';

    public array $departments = [];
    public array $titles = [];

    protected function initializeComponent()
    {
        $this->loadFilterOptions();
    }

    protected function getViewName(): string
    {
        return 'livewire.admin.user-list';
    }

    protected function getViewData(): array
    {
        return [
            'users' => $this->getUsers(),
            'departments' => $this->departments,
            'titles' => $this->titles,
        ];
    }

    protected function handlePropertyUpdate($property)
    {
        parent::handlePropertyUpdate($property);
        
        // Auto-search on filter changes
        if (in_array($property, ['selectedDepartment', 'selectedTitle', 'hasPhone', 'hasEmail'])) {
            $this->hasSearched = true;
        }
    }

    protected function clearComponentFilters()
    {
        $this->selectedDepartment = '';
        $this->selectedTitle = '';
        $this->hasPhone = false;
        $this->hasEmail = false;
    }

    public function getUsers()
    {
        if (!$this->hasSearched) {
            return collect();
        }

        $query = User::select(['id', 'name', 'surname', 'email', 'phone', 'title', 'department', 'photo', 'role']);

        // Apply search
        $query = $this->getSearchQuery($query, ['name', 'surname', 'email', 'title', 'department']);

        // Apply filters
        $query = $this->applyFilters($query);

        return $query->orderBy('name')->paginate(12);
    }

    private function loadFilterOptions()
    {
        $this->departments = cache()->remember('user_departments', 3600, function() {
            return User::select('department')
                ->distinct()
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->pluck('department')
                ->toArray();
        });
        
        $this->titles = cache()->remember('user_titles', 3600, function() {
            return User::select('title')
                ->distinct()
                ->whereNotNull('title')
                ->where('title', '!=', '')
                ->pluck('title')
                ->toArray();
        });
    }

    private function applyFilters($query)
    {
        return $query
            ->when($this->selectedDepartment, fn($q) => $q->where('department', $this->selectedDepartment))
            ->when($this->selectedTitle, fn($q) => $q->where('title', $this->selectedTitle))
            ->when($this->hasPhone, fn($q) => $q->whereNotNull('phone')->where('phone', '!=', ''))
            ->when($this->hasEmail, fn($q) => $q->whereNotNull('email')->where('email', '!=', ''));
    }
} 