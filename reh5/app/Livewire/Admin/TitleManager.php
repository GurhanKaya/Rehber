<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use App\Models\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout as LWLayout;

#[LWLayout('layouts.admin')]
class TitleManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Properties
    public $search = '';
    public $departmentFilter = '';
    public $showAddModal = false;
    public $showEditModal = false;
    public $editingTitle = null;
    
    // Form fields
    public $name = '';
    public $department_id = '';
    public $description = '';
    public $is_active = true;

    public function mount()
    {
        Gate::authorize('manageUsers');
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'department_id' => 'required|integer|exists:departments,id',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];

        if ($this->editingTitle) {
            $rules['name'] = [
                'required',
                'string', 
                'max:255',
                'unique:titles,name,' . $this->editingTitle->id . ',id,department_id,' . $this->department_id
            ];
        } else {
            $rules['name'] = [
                'required',
                'string',
                'max:255', 
                'unique:titles,name,NULL,id,department_id,' . $this->department_id
            ];
        }

        return $rules;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedDepartmentFilter()
    {
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->showAddModal = true;
    }

    public function openEditModal($titleId)
    {
        $this->editingTitle = Title::findOrFail($titleId);
        $this->name = $this->editingTitle->name;
        $this->department_id = $this->editingTitle->department_id;
        $this->description = $this->editingTitle->description;
        $this->is_active = $this->editingTitle->is_active;
        $this->showEditModal = true;
    }

    public function closeModals()
    {
        $this->showAddModal = false;
        $this->showEditModal = false;
        $this->editingTitle = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        Title::create([
            'name' => $this->name,
            'department_id' => $this->department_id,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', __('app.title_created_successfully'));
        $this->closeModals();
    }

    public function update()
    {
        $this->validate();

        $this->editingTitle->update([
            'name' => $this->name,
            'department_id' => $this->department_id,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', __('app.title_updated_successfully'));
        $this->closeModals();
    }

    public function delete($titleId)
    {
        $title = Title::findOrFail($titleId);
        
        // Check if title has users
        if ($title->users()->count() > 0) {
            session()->flash('error', __('app.cannot_delete_title_with_users'));
            return;
        }

        $title->delete();
        session()->flash('success', __('app.title_deleted_successfully'));
    }

    public function toggleStatus($titleId)
    {
        $title = Title::findOrFail($titleId);
        $title->update(['is_active' => !$title->is_active]);
        
        session()->flash('success', __('app.title_status_updated'));
    }

    private function resetForm()
    {
        $this->name = '';
        $this->department_id = '';
        $this->description = '';
        $this->is_active = true;
    }

    public function render()
    {
        $titles = Title::with('department')
            ->withCount('users')
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('department', function($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->when($this->departmentFilter, function($query) {
                $query->where('department_id', $this->departmentFilter);
            })
            ->orderBy('name')
            ->paginate(10);

        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.admin.title-manager', [
            'titles' => $titles,
            'departments' => $departments,
        ]);
    }
}


