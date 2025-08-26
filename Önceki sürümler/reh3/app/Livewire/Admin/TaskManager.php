<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class TaskManager extends Component
{
    use WithFileUploads, WithPagination;
    
    public $users;
    public $showModal = false;
    public $title;
    public $description;
    public $type = 'public';
    public $assigned_user_id;
    public $deadline;
    public $status = 'bekliyor';
    public $editId = null;
    public $files = [];
    public $uploadedFiles = [];
    public $existingFiles = [];
    public $showFilters = false;

    // Enhanced filtering
    public $search = '';
    public $typeFilter = '';
    public $statusFilter = '';
    public $assignedFilter = '';
    public $priorityFilter = '';
    public $deadlineFilter = '';
    public $appliedSearch = '';
    public $hasSearched = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        try {
            $this->users = User::where('role', 'personel')->orderBy('name')->get();
        } catch (\Exception $e) {
            $this->users = collect();
        }
    }

    public function searchTasks()
    {
        $this->appliedSearch = $this->search;
        $this->hasSearched = true;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->statusFilter = '';
        $this->assignedFilter = '';
        $this->priorityFilter = '';
        $this->deadlineFilter = '';
        $this->appliedSearch = '';
        $this->hasSearched = false;
        $this->resetPage();
    }

    public function updated($property)
    {
        if (in_array($property, ['typeFilter', 'statusFilter', 'assignedFilter', 'priorityFilter', 'deadlineFilter'])) {
            $this->resetPage();
        }
        
        // Search alanında otomatik arama yapmayın, sadece form submit ile
        if ($property === 'search') {
            return;
        }
    }

    public function showCreateModal()
    {
        $this->reset(['title', 'description', 'type', 'assigned_user_id', 'deadline', 'status', 'files', 'uploadedFiles', 'existingFiles', 'editId']);
        $this->type = 'public';
        $this->status = 'bekliyor';
        $this->assigned_user_id = null;
        $this->showModal = true;
    }

    public function showEditModal($id)
    {
        try {
            $task = Task::with(['assignedUser:id,name,surname', 'files'])->findOrFail($id);
            $this->editId = $task->id;
            $this->title = $task->title;
            $this->description = $task->description;
            $this->type = $task->type;
            $this->assigned_user_id = $task->assigned_user_id ?: null;
            $this->deadline = $task->deadline ? date('Y-m-d\TH:i', strtotime($task->deadline)) : null;
            $this->status = $task->status;
            $this->showModal = true;
            $this->files = [];
            $this->uploadedFiles = [];
            $this->existingFiles = $task->files ? $task->files->toArray() : [];
        } catch (\Exception $e) {
            session()->flash('error', 'Görev yüklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function updatedFiles()
    {
        foreach ($this->files as $file) {
            // Check for duplicate file names in current task (if editing)
            if ($this->editId) {
                $task = Task::find($this->editId);
                $existingFileNames = $task ? $task->files()->pluck('file_name')->toArray() : [];
                
                if (in_array($file->getClientOriginalName(), $existingFileNames)) {
                    session()->flash('error', 'Aynı isimli dosya zaten mevcut: ' . $file->getClientOriginalName());
                    continue;
                }
            }
            
            // Check for duplicate file names in currently uploaded files
            $currentFileNames = collect($this->uploadedFiles)->map(function($uploadedFile) {
                return method_exists($uploadedFile, 'getClientOriginalName') ? 
                       $uploadedFile->getClientOriginalName() : $uploadedFile;
            })->toArray();
            
            if (in_array($file->getClientOriginalName(), $currentFileNames)) {
                session()->flash('error', 'Bu dosya zaten yüklenmek üzere: ' . $file->getClientOriginalName());
                continue;
            }
            
            $this->uploadedFiles[] = $file;
        }
        $this->files = [];
    }

    public function removeFile($index)
    {
        array_splice($this->uploadedFiles, $index, 1);
    }

    public function removeExistingFile($fileId)
    {
        try {
            $file = \App\Models\TaskFile::find($fileId);
            if ($file && isset($file->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);
                $file->delete();
                $this->existingFiles = array_filter($this->existingFiles, fn($f) => $f['id'] != $fileId);
                session()->flash('success', 'Dosya başarıyla silindi.');
            } else {
                session()->flash('error', 'Dosya bulunamadı.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Dosya silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function saveTask()
    {
        // Dynamic validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:public,private,cooperative',
            'deadline' => 'nullable|date',
            'status' => 'required|in:bekliyor,devam ediyor,tamamlandı,iptal',
            'uploadedFiles.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt,xls,xlsx,ppt,pptx',
        ];

        // For private tasks, assigned user is required
        if ($this->type === 'private') {
            $rules['assigned_user_id'] = 'required|exists:users,id';
        } else {
            $rules['assigned_user_id'] = 'nullable|exists:users,id';
        }

        $validated = $this->validate($rules);
        
        // Ensure assigned_user_id is properly handled
        if (empty($validated['assigned_user_id']) || $validated['assigned_user_id'] === '') {
            $validated['assigned_user_id'] = null;
        }
        
        $validated['created_by'] = auth()->id();
        
        try {
            if ($this->editId) {
                $task = Task::with(['files', 'logs'])->findOrFail($this->editId);
                $task->update($validated);
                $this->saveFiles($task);
                
                // Log task update
                $task->logs()->create([
                    'user_id' => auth()->id(),
                    'action' => 'task_updated',
                    'details' => 'Görev güncellendi',
                ]);
                
                session()->flash('success', 'Görev başarıyla güncellendi.');
            } else {
                $task = Task::create($validated);
                $this->saveFiles($task);
                
                // Log task creation
                $task->logs()->create([
                    'user_id' => auth()->id(),
                    'action' => 'task_created',
                    'details' => 'Görev oluşturuldu',
                ]);
                
                session()->flash('success', 'Görev başarıyla oluşturuldu.');
            }
            
            $this->showModal = false;
            $this->reset(['title', 'description', 'type', 'assigned_user_id', 'deadline', 'status', 'files', 'uploadedFiles', 'existingFiles', 'editId']);
            $this->type = 'public';
            $this->status = 'bekliyor';
        } catch (\Exception $e) {
            session()->flash('error', 'Görev kaydedilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    protected function saveFiles($task)
    {
        // Get existing file names to check for duplicates
        $existingFileNames = $task->files ? $task->files->pluck('file_name')->toArray() : [];
        $uploadedCount = 0;
        
        if (!empty($this->uploadedFiles)) {
            foreach ($this->uploadedFiles as $file) {
                if (!$file || !method_exists($file, 'getClientOriginalName')) {
                    continue;
                }
                
                $originalFileName = $file->getClientOriginalName();
                
                // Check if file with same name already exists in the task
                if (in_array($originalFileName, $existingFileNames)) {
                    session()->flash('error', 'Aynı isimli dosya zaten mevcut: ' . $originalFileName);
                    continue;
                }
                
                // Generate secure filename
                $extension = $file->getClientOriginalExtension();
                $filename = uniqid() . '_' . time() . '.' . $extension;
                $path = $file->storeAs('tasks', $filename, 'public');
                
                $task->files()->create([
                    'file_path' => $path,
                    'file_name' => $originalFileName,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'user_id' => auth()->id(),
                ]);
                
                // Add to existing file names array to prevent duplicates in the same upload session
                $existingFileNames[] = $originalFileName;
                $uploadedCount++;
            }
        }
        
        // Log file uploads if any files were uploaded
        if ($uploadedCount > 0) {
            $task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'files_uploaded',
                'details' => "{$uploadedCount} dosya yüklendi",
            ]);
        }
        
        $this->uploadedFiles = [];
    }

    public function deleteTask($id)
    {
        try {
            $task = Task::with(['files', 'logs'])->findOrFail($id);
            
            // Log task deletion before deleting
            $task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'task_deleted',
                'details' => 'Görev silindi',
            ]);
            
            // Delete associated files
            if ($task->files) {
                foreach ($task->files as $file) {
                    if ($file && isset($file->file_path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);
                    }
                }
            }
            
            $task->delete();
            session()->flash('success', 'Görev başarıyla silindi.');
            $this->showModal = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Görev silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function showUnassignedTasks()
    {
        $this->assignedFilter = 'unassigned';
        $this->hasSearched = true;
        $this->resetPage();
    }

    public function getTasks()
    {
        $query = Task::with(['assignedUser:id,name,surname', 'creator:id,name,surname', 'participants:id,name,surname'])
            ->select(['id', 'title', 'description', 'type', 'assigned_user_id', 'created_by', 'status', 'deadline', 'created_at']);

        // Apply search filters
        if ($this->hasSearched && $this->appliedSearch !== '') {
            $search = $this->appliedSearch;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('assignedUser', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('surname', 'like', "%{$search}%");
                  });
            });
        }

        // Apply filters
        if ($this->typeFilter !== '') {
            $query->where('type', $this->typeFilter);
        }
        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }
        if ($this->assignedFilter !== '') {
            if ($this->assignedFilter === 'unassigned') {
                // Ana atanan kişisi olmayan VE katılımcısı da olmayan görevler
                $query->whereNull('assigned_user_id')
                      ->whereDoesntHave('participants');
            } else {
                $query->where('assigned_user_id', $this->assignedFilter);
            }
        }
        if ($this->deadlineFilter !== '') {
            switch ($this->deadlineFilter) {
                case 'today':
                    $query->whereDate('deadline', now()->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('deadline', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'overdue':
                    $query->where('deadline', '<', now())->where('status', '!=', 'tamamlandı');
                    break;
            }
        }

        return $query->orderByDesc('created_at')->paginate(12);
    }

    public function render()
    {
        return view('livewire.admin.task-manager', [
            'tasks' => $this->getTasks(),
        ])->layout('layouts.admin');
    }
}

