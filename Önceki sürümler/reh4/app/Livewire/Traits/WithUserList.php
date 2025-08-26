<?php

namespace App\Livewire\Traits;

use App\Models\User;

trait WithUserList
{
    // User-specific filter properties
    public string $selectedDepartment = '';
    public string $selectedTitle = '';
    public bool $hasPhone = false;
    public bool $hasEmail = false;

    public array $departments = [];
    public array $titles = [];

    /**
     * Apply user-specific filters to a query
     */
    protected function applyUserFilters($query)
    {
        return $query
            ->when($this->selectedDepartment, fn($q) => $q->where('department', $this->selectedDepartment))
            ->when($this->selectedTitle, fn($q) => $q->where('title', $this->selectedTitle))
            ->when($this->hasPhone, fn($q) => $q->whereNotNull('phone')->where('phone', '!=', ''))
            ->when($this->hasEmail, fn($q) => $q->whereNotNull('email')->where('email', '!=', ''));
    }

    /**
     * Load department options (optionally by role)
     */
    protected function loadDepartmentOptions(?string $role = null): void
    {
        $cacheKey = $role ? "user_departments_{$role}" : 'user_departments';
        
        $this->departments = cache()->remember($cacheKey, 3600, function() use ($role) {
            $query = User::select('department')
                ->distinct()
                ->whereNotNull('department')
                ->where('department', '!=', '');
            
            if ($role) {
                $query->where('role', $role);
            }
            
            return $query->pluck('department')->toArray();
        });
    }

    /**
     * Load title options (optionally by role)
     */
    protected function loadTitleOptions(?string $role = null): void
    {
        $cacheKey = $role ? "user_titles_{$role}" : 'user_titles';
        
        $this->titles = cache()->remember($cacheKey, 3600, function() use ($role) {
            $query = User::select('title')
                ->distinct()
                ->whereNotNull('title')
                ->where('title', '!=', '');
            
            if ($role) {
                $query->where('role', $role);
            }
            
            return $query->pluck('title')->toArray();
        });
    }

    /**
     * Load both filter option sets
     */
    protected function loadFilterOptions(?string $role = null): void
    {
        $this->loadDepartmentOptions($role);
        $this->loadTitleOptions($role);
    }
}
