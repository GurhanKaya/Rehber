<?php

namespace App\Livewire\Traits;

trait WithTaskList
{
    public string $status = '';
    public string $type = '';

    /**
     * Apply task-specific filters to a query
     */
    protected function applyTaskFilters($query)
    {
        return $query
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->type, fn($q) => $q->where('type', $this->type));
    }

    protected function getStatusOptions(): array
    {
        return [
            'bekliyor' => 'Bekliyor',
            'devam ediyor' => 'Devam Ediyor',
            'tamamlandı' => 'Tamamlandı',
            'iptal' => 'İptal',
        ];
    }

    protected function getTypeOptions(): array
    {
        return [
            'public' => 'Genel',
            'assigned' => 'Atanmış',
            'cooperative' => 'İşbirliği',
        ];
    }
}
