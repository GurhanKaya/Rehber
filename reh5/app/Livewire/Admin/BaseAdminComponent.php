<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;

abstract class BaseAdminComponent extends Component
{
    use WithPagination;

    public $layout = 'layouts.admin';
    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->initializeComponent();
    }

    public function updated($property)
    {
        $this->handlePropertyUpdate($property);
    }

    public function render()
    {
        return view($this->getViewName(), $this->getViewData());
    }

    // Abstract methods to be implemented by child components
    abstract protected function getViewName(): string;
    
    protected function getViewData(): array
    {
        return [];
    }

    protected function initializeComponent()
    {
        // Override in child components if needed
    }

    protected function handlePropertyUpdate($property)
    {
        // Override in child components if needed
        $this->resetPage();
    }

    protected function getFilterableProperties(): array
    {
        return [];
    }

    protected function resetPage()
    {
        if (method_exists($this, 'resetPage')) {
            parent::resetPage();
        }
    }
} 