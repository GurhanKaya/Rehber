<?php

namespace App\Livewire\Base;

use App\Models\User;
use App\Livewire\Traits\WithUserList;

abstract class BaseUserListComponent extends BaseListComponent
{
    use WithUserList;

    /**
     * Model sınıfı
     */
    protected function getModel(): string
    {
        return User::class;
    }

    /**
     * Arama alanları
     */
    protected function getSearchFields(): array
    {
        return ['name', 'surname', 'email', 'department.name', 'title.name'];
    }

    /**
     * Filtre alanları
     */
    protected function getFilterFields(): array
    {
        return ['department_id', 'title_id', 'hasPhone', 'hasEmail'];
    }

    /**
     * Sıralama alanları
     */
    protected function getOrderBy(): array
    {
        return ['name' => 'asc', 'surname' => 'asc'];
    }

    /**
     * Temel kullanıcı query'si
     */
    protected function getBaseQuery()
    {
        $query = parent::getBaseQuery();
        
        // Role filtresi uygula (child component'lerde override edilebilir)
        return $this->applyRoleFilter($query);
    }

    /**
     * Role filtresi uygula
     */
    protected function applyRoleFilter($query)
    {
        // Child component'lerde override edilecek
        return $query;
    }

    /**
     * Component başlatma
     */
    protected function initializeComponent()
    {
        // Filtre seçeneklerini yükle
        $this->loadFilterOptions();
    }

    /**
     * Filtre seçeneklerini yükle
     */
    protected function loadFilterOptions(): void
    {
        $this->loadDepartmentOptions();
        $this->loadTitleOptions();
    }

    /**
     * Component-specific filtreleri temizle
     */
    protected function clearComponentFilters()
    {
        $this->selectedDepartment = '';
        $this->selectedTitle = '';
        $this->hasPhone = false;
        $this->hasEmail = false;
    }

    /**
     * Property güncelleme handler
     */
    protected function handlePropertyUpdate($property)
    {
        parent::handlePropertyUpdate($property);
        
        // Auto-search on filter changes
        if (in_array($property, ['selectedDepartment', 'selectedTitle', 'hasPhone', 'hasEmail'])) {
            $this->searched = true;
        }
    }

    /**
     * Filtreleri uygula
     */
    protected function applyFilters($query)
    {
        return $this->applyUserFilters($query);
    }

    /**
     * Sonuçları getir
     */
    protected function getResults()
    {
        return parent::getResults();
    }

    /**
     * View data
     */
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
