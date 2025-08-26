<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public string $query = '';
    public string $selectedDepartment = '';
    public string $selectedTitle = '';
    public bool $hasPhone = false;
    public bool $hasEmail = false;
    public bool $showFilters = false;
    public string $viewMode = 'grid';
    public bool $searched = false;

    public array $departments = [];
    public array $titles = [];

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        // Optimize with single queries and caching
        $this->departments = cache()->remember('user_departments', 3600, function() {
            return User::select('department')->distinct()->whereNotNull('department')->where('department', '!=', '')->pluck('department')->toArray();
        });
        
        $this->titles = cache()->remember('user_titles', 3600, function() {
            return User::select('title')->distinct()->whereNotNull('title')->where('title', '!=', '')->pluck('title')->toArray();
        });
    }

    public function updated($property): void
    {
        $this->resetPage();
        
        // Auto-search on filter changes for better UX
        if (in_array($property, ['selectedDepartment', 'selectedTitle', 'hasPhone', 'hasEmail'])) {
            $this->searched = true;
        }
    }

    public function search(): void
    {
        $this->searched = true;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->query = '';
        $this->selectedDepartment = '';
        $this->selectedTitle = '';
        $this->hasPhone = false;
        $this->hasEmail = false;
        $this->searched = false;
        $this->resetPage();
    }

    public function toggleFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    public function getUsers()
    {
        if (!$this->searched) {
            return collect(); // Return empty collection if no search performed
        }

        return User::query()
            ->select(['id', 'name', 'surname', 'email', 'phone', 'title', 'department', 'photo', 'role'])
            ->when($this->query, fn($q) =>
                $q->where(function ($subQ) {
                    $search = '%' . $this->query . '%';
                    $subQ->where('name', 'like', $search)
                        ->orWhere('surname', 'like', $search)
                        ->orWhere('title', 'like', $search)
                        ->orWhere('department', 'like', $search);
                })
            )
            ->when($this->selectedDepartment, fn($q) =>
                $q->where('department', $this->selectedDepartment)
            )
            ->when($this->selectedTitle, fn($q) =>
                $q->where('title', $this->selectedTitle)
            )
            ->when($this->hasPhone, fn($q) =>
                $q->whereNotNull('phone')->where('phone', '!=', '')
            )
            ->when($this->hasEmail, fn($q) =>
                $q->whereNotNull('email')->where('email', '!=', '')
            )
            ->orderBy('name')
            ->orderBy('surname')
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.admin.user-list', [
            'users' => $this->getUsers(),
            'searched' => $this->searched,
        ])->layout('layouts.admin');
    }
}
