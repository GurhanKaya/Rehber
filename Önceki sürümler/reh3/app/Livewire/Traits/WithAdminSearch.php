<?php

namespace App\Livewire\Traits;

trait WithAdminSearch
{
    public $search = '';
    public $appliedSearch = '';
    public $hasSearched = false;
    public $showFilters = false;

    public function searchData()
    {
        $this->appliedSearch = $this->search;
        $this->hasSearched = true;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->appliedSearch = '';
        $this->hasSearched = false;
        $this->showFilters = false;
        $this->resetPage();
        
        // Override this method in components for specific filters
        $this->clearComponentFilters();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    protected function clearComponentFilters()
    {
        // Override in components to clear specific filters
    }

    protected function getSearchQuery($query, $searchFields)
    {
        if (!$this->hasSearched || empty($this->appliedSearch)) {
            return $query;
        }

        return $query->where(function ($q) use ($searchFields) {
            $search = '%' . $this->appliedSearch . '%';
            
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
} 