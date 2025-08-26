<?php

namespace App\Livewire\Personel;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class TaskList extends Component
{
    use WithPagination;

    public $query = '';
    public $searched = false;
    public $status = '';
    public $type = '';
    public $onlyDeadlineToday = false;
    public $onlyMyTasks = false;
    public $showFilters = false;

    protected $paginationTheme = 'tailwind';

    public function search()
    {
        $this->searched = true;
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->searched = false;
        $this->status = '';
        $this->type = '';
        $this->onlyDeadlineToday = false;
        $this->onlyMyTasks = false;
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function filterDeadlineToday()
    {
        $this->onlyDeadlineToday = !$this->onlyDeadlineToday;
        $this->onlyMyTasks = false;
        $this->resetPage();
    }

    public function filterMyTasks()
    {
        $this->onlyMyTasks = !$this->onlyMyTasks;
        $this->onlyDeadlineToday = false;
        $this->resetPage();
    }

    public function updated($property)
    {
        if (in_array($property, ['status', 'type'])) {
            $this->resetPage();
        }
        
        // Search alanında otomatik arama yapmayın, sadece form submit ile
        if ($property === 'query') {
            return;
        }
    }

    public function getTasks()
    {
        $user = auth()->user();
        
        // Önce kullanıcının erişebileceği görev ID'lerini al
        $accessibleTaskIds = $this->getAccessibleTaskIds($user);
        
        // Ana query - sadece erişilebilir görevler
        $query = Task::with(['assignedUser:id,name,surname', 'participants:id,name,surname', 'files:id,task_id,file_name,file_path', 'creator:id,name,surname'])
            ->whereIn('id', $accessibleTaskIds);

        // Arama filtresi
        if ($this->query) {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->query}%")
                  ->orWhere('description', 'like', "%{$this->query}%");
            });
        }

        // Status filtresi
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Type filtresi
        if ($this->type) {
            if ($this->type === 'cooperative') {
                $query->where('type', 'cooperative');
            } else {
                $query->where('type', $this->type);
            }
        }



        // Deadline today filtresi
        if ($this->onlyDeadlineToday) {
            $query->whereDate('deadline', now()->toDateString());
        }

        // Only my assigned tasks filtresi
        if ($this->onlyMyTasks) {
            $query->where('assigned_user_id', $user->id);
        }

        // Default ordering
        if (!$this->query && !$this->status && !$this->type && !$this->onlyDeadlineToday && !$this->onlyMyTasks) {
            $today = now()->toDateString();
            $query->orderByRaw(
                "CASE ".
                "WHEN DATE(deadline) = ? AND status = 'bekliyor' THEN 0 ".
                "WHEN status = 'devam ediyor' THEN 1 ".
                "WHEN status = 'bekliyor' THEN 2 ".
                "ELSE 3 END", [$today]
            );
        }

        return $query->orderByDesc('created_at')->paginate(12);
    }

    private function getAccessibleTaskIds($user)
    {
        // Kullanıcının erişebileceği görev ID'lerini al
        $assignedTaskIds = Task::where('assigned_user_id', $user->id)->pluck('id');
        
        // Cooperative görevler sadece katılımcı olan kullanıcılar için görüntülenebilir
        $cooperativeTaskIds = Task::where('type', 'cooperative')
            ->whereHas('participants', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->pluck('id');
        
        return $assignedTaskIds->merge($cooperativeTaskIds)->unique();
    }

    public function render()
    {
        return view('livewire.personel.task-list', [
            'tasks' => $this->getTasks(),
            'searched' => $this->searched,
        ])->layout('layouts.personel');
    }
} 