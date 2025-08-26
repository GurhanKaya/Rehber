<?php

namespace App\Livewire\Personel;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public string $query = '';
    public bool $searched = false;
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
        // Sadece personel rolündeki kullanıcıların departman ve unvan bilgilerini al
        $this->departments = cache()->remember('personel_departments', 3600, function() {
            return User::where('role', 'personel')
                ->select('department')
                ->distinct()
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->pluck('department')
                ->toArray();
        });
        
        $this->titles = cache()->remember('personel_titles', 3600, function() {
            return User::where('role', 'personel')
                ->select('title')
                ->distinct()
                ->whereNotNull('title')
                ->where('title', '!=', '')
                ->pluck('title')
                ->toArray();
        });
    }

    public function search(): void
    {
        $this->searched = true;
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->query = '';
        $this->searched = false;
        $this->selectedDepartment = '';
        $this->selectedTitle = '';
        $this->hasPhone = false;
        $this->hasEmail = false;
        $this->resetPage();
    }

    public function toggleFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    public function filterByPhone(): void
    {
        $this->hasPhone = !$this->hasPhone;
        $this->hasEmail = false;
        $this->resetPage();
    }

    public function filterByEmail(): void
    {
        $this->hasEmail = !$this->hasEmail;
        $this->hasPhone = false;
        $this->resetPage();
    }

    public function updated($property): void
    {
        if (in_array($property, ['selectedDepartment', 'selectedTitle'])) {
            $this->resetPage();
        }
        
        // Search alanında otomatik arama yapmayın, sadece form submit ile
        if ($property === 'search') {
            return;
        }
    }

    public function getUsers()
    {
        // Önce personel rolündeki kullanıcı ID'lerini al
        $personelUserIds = $this->getPersonelUserIds();
        
        // Ana query - sadece personel rolündeki kullanıcılar
        $query = User::select(['id', 'name', 'surname', 'email', 'phone', 'title', 'department', 'photo', 'role'])
            ->whereIn('id', $personelUserIds);

        // Arama filtresi
        if ($this->query) {
            $query->where(function ($subQ) {
                $subQ->where('name', 'like', '%' . $this->query . '%')
                    ->orWhere('surname', 'like', '%' . $this->query . '%')
                    ->orWhere('title', 'like', '%' . $this->query . '%')
                    ->orWhere('department', 'like', '%' . $this->query . '%');
            });
        }

        // Department filtresi
        if ($this->selectedDepartment) {
            $query->where('department', $this->selectedDepartment);
        }

        // Title filtresi
        if ($this->selectedTitle) {
            $query->where('title', $this->selectedTitle);
        }

        // Phone filtresi
        if ($this->hasPhone) {
            $query->whereNotNull('phone')->where('phone', '!=', '');
        }

        // Email filtresi
        if ($this->hasEmail) {
            $query->whereNotNull('email')->where('email', '!=', '');
        }

        return $query->orderBy('name')->orderBy('surname')->paginate(12);
    }

    private function getPersonelUserIds()
    {
        // Sadece personel rolündeki kullanıcıların ID'lerini al
        return User::where('role', 'personel')->pluck('id');
    }

    public function render()
    {
        return view('livewire.personel.user-list', [
            'users' => $this->getUsers(),
            'searched' => $this->searched,
        ])->layout('layouts.personel');
    }
} 