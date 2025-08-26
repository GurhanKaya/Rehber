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
            ->when($this->selectedDepartment, fn($q) => $q->where('department_id', $this->selectedDepartment))
            ->when($this->selectedTitle, fn($q) => $q->where('title_id', $this->selectedTitle))
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
            $query = \App\Models\Department::select('id', 'name')
                ->where('is_active', true);
            
            if ($role) {
                $query->whereHas('users', function($q) use ($role) {
                    $q->where('role', $role);
                });
            }
            
            return $query->pluck('name', 'id')->toArray();
        });
    }

    /**
     * Load title options (optionally by role)
     */
    protected function loadTitleOptions(?string $role = null): void
    {
        $cacheKey = $role ? "user_titles_{$role}" : 'user_titles';
        
        $this->titles = cache()->remember($cacheKey, 3600, function() use ($role) {
            $query = \App\Models\Title::select('id', 'name')
                ->where('is_active', true);
            
            if ($role) {
                $query->whereHas('users', function($q) use ($role) {
                    $q->where('role', $role);
                });
            }
            
            return $query->pluck('name', 'id')->toArray();
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
