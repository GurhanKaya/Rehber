<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use App\Models\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout as LWLayout;

#[LWLayout('layouts.admin')]
class DepartmentManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Properties
    public $search = '';
    public $showAddModal = false;
    public $showEditModal = false;
    public $editingDepartment = null;
    
    // Form fields
    public $name = '';
    public $description = '';
    public $is_active = true;

    public function mount()
    {
        Gate::authorize('manageUsers');
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];

        if ($this->editingDepartment) {
            $rules['name'] = 'required|string|max:255|unique:departments,name,' . $this->editingDepartment->id;
        }

        return $rules;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->showAddModal = true;
    }

    public function openEditModal($departmentId)
    {
        $this->editingDepartment = Department::findOrFail($departmentId);
        $this->name = $this->editingDepartment->name;
        $this->description = $this->editingDepartment->description;
        $this->is_active = $this->editingDepartment->is_active;
        $this->showEditModal = true;
    }

    public function closeModals()
    {
        $this->showAddModal = false;
        $this->showEditModal = false;
        $this->editingDepartment = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        Department::create([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', __('app.department_created_successfully'));
        $this->closeModals();
    }

    public function update()
    {
        $this->validate();

        $this->editingDepartment->update([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', __('app.department_updated_successfully'));
        $this->closeModals();
    }

    public function delete($departmentId)
    {
        $department = Department::findOrFail($departmentId);
        
        // Check if department has users
        if ($department->users()->count() > 0) {
            session()->flash('error', __('app.cannot_delete_department_with_users'));
            return;
        }

        // Delete associated titles first
        $department->titles()->delete();
        $department->delete();

        session()->flash('success', __('app.department_deleted_successfully'));
    }

    public function toggleStatus($departmentId)
    {
        $department = Department::findOrFail($departmentId);
        $department->update(['is_active' => !$department->is_active]);
        
        session()->flash('success', __('app.department_status_updated'));
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->is_active = true;
    }

    public function render()
    {
        $departments = Department::withCount(['users', 'titles'])
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.department-manager', [
            'departments' => $departments,
        ]);
    }
}


