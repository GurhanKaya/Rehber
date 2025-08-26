<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserSearch extends Component
{
    use WithPagination;

    public string $query = '';
    public string $selectedDepartment = '';
    public string $selectedTitle = '';
    public bool $hasPhone = false;
    public bool $hasEmail = false;
    public bool $showFilters = false;
    public string $viewMode = 'grid';

    public array $departments = [];
    public array $titles = [];

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->departments = User::select('department')->distinct()->pluck('department')->filter()->toArray();
        $this->titles = User::select('title')->distinct()->pluck('title')->filter()->toArray();
    }

    public function updated($property): void
    {
        $this->resetPage();
    }

    public function search(): void
    {
        $this->resetPage();
    }

    public function toggleFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    public function getUsersProperty()
    {
        return User::query()
            ->when($this->query, fn($q) =>
                $q->where(function ($subQ) {
                    $subQ->where('name', 'like', '%' . $this->query . '%')
                        ->orWhere('surname', 'like', '%' . $this->query . '%')
                        ->orWhere('title', 'like', '%' . $this->query . '%')
                        ->orWhere('department', 'like', '%' . $this->query . '%');
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
            ->orderBy('id', 'desc')
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.user-search', [
            'users' => $this->users,
        ])->layout('layouts.guest');
    }
}