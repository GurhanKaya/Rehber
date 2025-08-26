<?php

namespace App\Livewire\Base;

use Livewire\Component;
use Livewire\WithPagination;

abstract class BaseListComponent extends Component
{
    use WithPagination { resetPage as protected paginateResetPage; }

    // Temel özellikler
    protected $paginationTheme = 'tailwind';
    protected $perPage = 12;
    protected $defaultOrderBy = 'created_at';
    protected $defaultOrderDirection = 'desc';

    // Ortak property'ler
    public bool $searched = false;
    public bool $showFilters = false;
    public string $query = '';
    public string $viewMode = 'grid';

    // Abstract methods to be implemented by child components
    abstract protected function getModel(): string;
    abstract protected function getSearchFields(): array;
    abstract protected function getFilterFields(): array;
    abstract protected function getOrderBy(): array;
    abstract protected function getViewName(): string;
    // getLayout kaldırıldı; Layout attribute ile tanımlanmalı

    /**
     * Component mount
     */
    public function mount()
    {
        $this->initializeComponent();
    }

    /**
     * Component başlatma
     */
    protected function initializeComponent()
    {
        // Override in child components if needed
    }

    /**
     * Property güncellemelerini handle et
     */
    public function updated($property)
    {
        $this->handlePropertyUpdate($property);
    }

    /**
     * Property güncelleme handler
     */
    protected function handlePropertyUpdate($property)
    {
        // Override in child components if needed
        $this->resetPage();
    }

    /**
     * Arama yap
     */
    public function search(): void
    {
        $this->searched = true;
        $this->resetPage();
    }

    /**
     * Aramayı temizle (guest toolbar ile uyumlu)
     */
    public function clearSearch(): void
    {
        $this->query = '';
        $this->searched = false;
        $this->resetPage();
        $this->clearComponentFilters();
    }

    /**
     * Filtreleri temizle
     */
    public function clearFilters(): void
    {
        $this->resetPage();
        $this->clearComponentFilters();
    }

    /**
     * Component-specific filtreleri temizle
     */
    protected function clearComponentFilters()
    {
        // Override in child components
    }

    /**
     * Filtreleri göster/gizle
     */
    public function toggleFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    /**
     * Arama filtresi uygula
     */
    protected function applySearch($query, $searchTerm, $searchFields = null)
    {
        if (empty($searchTerm)) {
            return $query;
        }

        $searchFields = $searchFields ?? $this->getSearchFields();
        $search = '%' . $searchTerm . '%';

        return $query->where(function ($q) use ($searchFields, $search) {
            foreach ($searchFields as $field) {
                if (str_contains($field, '.')) {
                    // Relation field
                    [$relation, $relationField] = explode('.', $field);
                    $q->orWhereHas($relation, function ($subQ) use ($relationField, $search) {
                        $subQ->where($relationField, 'like', $search);
                    });
                } else {
                    // Direct field
                    $q->orWhere($field, 'like', $search);
                }
            }
        });
    }

    /**
     * Filtreleri uygula
     */
    protected function applyFilters($query)
    {
        // Override in child components
        return $query;
    }

    /**
     * Sıralama uygula
     */
    protected function applyOrderBy($query)
    {
        $orderBy = $this->getOrderBy();
        
        if (empty($orderBy)) {
            return $query->orderBy($this->defaultOrderBy, $this->defaultOrderDirection);
        }

        foreach ($orderBy as $field => $direction) {
            if (is_numeric($field)) {
                $query->orderBy($direction, $this->defaultOrderDirection);
            } else {
                $query->orderBy($field, $direction);
            }
        }

        return $query;
    }

    /**
     * Sonuçları getir
     */
    protected function getResults()
    {
        $query = $this->getBaseQuery();
        $query = $this->applySearch($query, $this->query ?? '');
        $query = $this->applyFilters($query);
        $query = $this->applyOrderBy($query);
        
        return $query->paginate($this->perPage);
    }

    /**
     * Temel query
     */
    protected function getBaseQuery()
    {
        $model = $this->getModel();
        return $model::query();
    }

    /**
     * Sayfa sıfırla (trait üzerinden)
     */
    protected function resetPage()
    {
        $this->paginateResetPage();
    }

    /**
     * Render
     */
    public function render()
    {
        return view($this->getViewName(), $this->getViewData());
    }

    /**
     * View data
     */
    protected function getViewData(): array
    {
        return [
            'items' => $this->getResults(),
            'searched' => $this->searched ?? false,
        ];
    }
}
