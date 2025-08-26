<?php

namespace App\Livewire\Personel;

use App\Livewire\Base\BaseUserListComponent;
use Livewire\Attributes\Layout;

#[Layout('layouts.personel')]
class UserList extends BaseUserListComponent
{
    /**
     * View adı
     */
    protected function getViewName(): string
    {
        return 'livewire.personel.user-list';
    }

    /**
     * Component başlatma
     */
    protected function initializeComponent()
    {
        // Authorization kontrolü
        $this->authorize('viewPersonelPanel');
        
        // Sadece personel rolündeki kullanıcılar için filtre seçenekleri
        $this->loadFilterOptions();
    }

    /**
     * Role filtresi uygula (sadece personel rolündeki kullanıcılar)
     */
    protected function applyRoleFilter($query)
    {
        return $query->where('role', 'personel');
    }

    /**
     * Filtre seçeneklerini yükle
     */
    protected function loadFilterOptions(): void
    {
        // Sadece personel rolündeki kullanıcılar için filtre seçenekleri
        $this->loadDepartmentOptions('personel');
        $this->loadTitleOptions('personel');
    }

    /**
     * Component-specific filtreleri temizle
     */
    protected function clearComponentFilters()
    {
        parent::clearComponentFilters();
        
        // Personel'e özel ek filtreler varsa buraya eklenebilir
    }

} 