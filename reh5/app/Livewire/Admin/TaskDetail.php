<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Task;
use App\Models\User;

class TaskDetail extends Component
{
    use WithFileUploads;

    public $layout = 'layouts.admin';

    public $task;
    public $users;
    public $title;
    public $description;
    public $type;
    public $assigned_user_id;
    public $deadline;
    public $status;
    public $newStatus;
    public $newFiles = [];
    public $newComment = '';
    public $participants = [];
    public $selectedParticipants = [];
    public $editMode = false;
    public $searchQuery = '';
    public $searchResults;
    public $assignedUsers = [];

    public function mount(Task $task)
    {
        $this->task = $task->load('files', 'assignedUser', 'comments.user', 'logs.user', 'participants');
        $this->users = User::whereIn('role', ['personel', 'admin'])->orderBy('name')->get();
        $this->title = $task->title;
        $this->description = $task->description;
        $this->type = $task->type;
        $this->assigned_user_id = $task->assigned_user_id;
        $this->deadline = $task->deadline ? $task->deadline->format('Y-m-d\TH:i') : null;
        $this->status = $task->status;
        $this->newStatus = $task->status;
        $this->participants = $this->task->participants;
        $this->selectedParticipants = $this->task->participants->pluck('id')->toArray();
        
        // Load assigned users (including main assigned user and participants)
        $this->loadAssignedUsers();
        
        // Initialize search results as empty collection
        $this->searchResults = collect();
    }

    private function loadAssignedUsers()
    {
        $assignedUsers = [];
        
        // Ana atanan kullanıcıyı ekle
        if ($this->task->assignedUser) {
            $assignedUsers[] = $this->task->assignedUser;
        }
        
        // Katılımcıları ekle
        foreach ($this->task->participants as $participant) {
            $exists = false;
            foreach ($assignedUsers as $existingUser) {
                if ($existingUser->id == $participant->id) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $assignedUsers[] = $participant;
            }
        }
        
        $this->assignedUsers = $assignedUsers;
    }

    public function updateTask()
    {
        $this->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:public,private,cooperative',
            'deadline' => 'nullable|date|after_or_equal:today',
            'status' => 'required|in:bekliyor,devam ediyor,tamamlandı,iptal',
        ]);

        // Get assigned user IDs from assignedUsers array
        $assignedUserIds = [];
        foreach ($this->assignedUsers as $user) {
            $assignedUserIds[] = $user->id;
        }
        
        // Set main assigned user (first one if exists)
        $mainAssignedUserId = count($assignedUserIds) > 0 ? $assignedUserIds[0] : null;
        
        // Store old values for logging
        $oldTitle = $this->task->title;
        $oldDescription = $this->task->description;
        $oldType = $this->task->type;
        $oldAssignedUserId = $this->task->assigned_user_id;
        $oldDeadline = $this->task->deadline;
        $oldStatus = $this->task->status;
        
        // Store old assigned users for comparison
        $oldAssignedUserIds = [];
        if ($this->task->assigned_user_id) {
            $oldAssignedUserIds[] = $this->task->assigned_user_id;
        }
        $oldParticipantIds = $this->task->participants->pluck('id')->toArray();
        
        $this->task->update([
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'assigned_user_id' => $mainAssignedUserId,
            'deadline' => $this->deadline,
            'status' => $this->status,
        ]);
        
        // Update participants (all assigned users except the main one)
        $participantIds = array_slice($assignedUserIds, 1);
        $this->task->participants()->sync($participantIds);
        
        $this->task->refresh();
        $this->participants = $this->task->participants;
        
        // Update assignedUsers to reflect the current state
        $this->loadAssignedUsers();
        
        // Log the changes
        $changes = [];
        if ($oldTitle !== $this->title) $changes[] = 'Başlık değiştirildi';
        if ($oldDescription !== $this->description) $changes[] = 'Açıklama değiştirildi';
        
        if (!empty($changes)) {
            $this->task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'task_updated',
                'details' => implode(', ', $changes),
            ]);
        }

        // Eğer atanan kişi değiştiyse ve yeni atanan kişi varsa, detaylı atama log kaydı ekle
        if ($oldAssignedUserId !== $mainAssignedUserId && $mainAssignedUserId) {
            $oldUser = $oldAssignedUserId ? User::find($oldAssignedUserId) : null;
            $newUser = User::find($mainAssignedUserId);
            
            $oldUserName = $oldUser ? $oldUser->name . ' ' . $oldUser->surname : 'Atanmamış';
            $newUserName = $newUser->name . ' ' . $newUser->surname;
            
            $this->task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'user_assigned',
                'old_value' => $oldUserName,
                'new_value' => $newUserName,
                'details' => $newUserName,
            ]);
        }
        
        // Eğer deadline değiştiyse, detaylı deadline log kaydı ekle
        if ($oldDeadline != $this->deadline) {
            $oldDeadlineStr = $oldDeadline ? $oldDeadline->format('d.m.Y') : 'not_specified';
            $newDeadlineStr = $this->deadline ? $this->deadline->format('d.m.Y') : 'not_specified';
            
            $this->task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'deadline_changed',
                'old_value' => $oldDeadlineStr,
                'new_value' => $newDeadlineStr,
                'details' => "{$oldDeadlineStr} → {$newDeadlineStr}",
            ]);
        }
        
        // Eğer task type değiştiyse, detaylı type log kaydı ekle
        if ($oldType !== $this->type) {
            $this->task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'task_type_changed',
                'old_value' => $oldType,
                'new_value' => $this->type,
                'details' => $oldType . " → " . $this->type,
            ]);
        }
        
        // Eğer status değiştiyse, detaylı status log kaydı ekle
        if ($oldStatus !== $this->status) {
            $this->task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'status_updated',
                'old_value' => $oldStatus,
                'new_value' => $this->status,
                'details' => $oldStatus . " → " . $this->status,
            ]);
        }
        
        // Log user additions and removals
        $this->logUserChanges($oldAssignedUserIds, $oldParticipantIds, $assignedUserIds, $participantIds);
        
        session()->flash('success', __('app.task_updated_success'));
    }

    public function updateStatus()
    {
        if (in_array($this->newStatus, ['bekliyor', 'devam ediyor', 'tamamlandı', 'iptal'])) {
            $oldStatus = $this->task->status;
            $this->task->status = $this->newStatus;
            $this->task->save();
            $this->task->refresh();
            
            // Log status change
            $this->task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'status_updated',
                'old_value' => $oldStatus,
                'new_value' => $this->newStatus,
                'details' => $oldStatus . " → " . $this->newStatus,
            ]);
            
            session()->flash('success', __('app.status_updated_success'));
        }
    }

    public function addParticipant($userId)
    {
        if (!in_array($userId, $this->selectedParticipants)) {
            $this->selectedParticipants[] = $userId;
        }
    }

    public function removeParticipant($userId)
    {
        $this->selectedParticipants = array_filter($this->selectedParticipants, fn($id) => $id != $userId);
    }

    public function updatedNewFiles()
    {
        if ($this->newFiles && count($this->newFiles) > 0) {
            $this->uploadFiles();
        }
    }

    public function uploadFiles()
    {
        $this->validate([
            'newFiles.*' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,txt',
        ]);

        $duplicateNames = [];
        $uploadedCount = 0;

        foreach ($this->newFiles as $file) {
            $originalName = $file->getClientOriginalName();
            $exists = $this->task->files()->where('file_name', $originalName)->exists();
            
            if ($exists) {
                $duplicateNames[] = $originalName;
                continue;
            }
            
            // Generate secure filename
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $path = $file->storeAs('tasks', $filename, 'local');
            
            $this->task->files()->create([
                'file_path' => $path,
                'file_name' => $originalName,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'user_id' => auth()->id(),
            ]);
            
            $uploadedCount++;
        }

        if ($uploadedCount > 0) {
            // Get uploaded file names
            $uploadedFileNames = [];
            foreach ($this->newFiles as $file) {
                $uploadedFileNames[] = $file->getClientOriginalName();
            }
            
            // Log file upload
            $this->task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'files_uploaded',
                'new_value' => '',
                'details' => implode(', ', $uploadedFileNames),
            ]);
            
            // Update task's updated_at timestamp
            $this->task->touch();
            
            session()->flash('success', __('app.files_uploaded_success', ['count' => $uploadedCount]));
        }
        
        $this->task->refresh();
        $this->newFiles = [];

        if ($duplicateNames) {
            session()->flash('error', __('app.duplicate_file_exists'));
        }
    }

    public function deleteFile($fileId)
    {
        $file = $this->task->files()->where('id', $fileId)->first();
        if ($file) {
            $fileName = $file->file_name;
            
            // Önce local storage'da kontrol et (yeni dosyalar)
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($file->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($file->file_path);
            }
            // Sonra public storage'da kontrol et (eski dosyalar)
            elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($file->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);
            }
            
            $file->delete();
            
            // Log file deletion
            $this->task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'file_deleted',
                'old_value' => $fileName,
                'details' => $fileName,
            ]);
            
            // Update task's updated_at timestamp
            $this->task->touch();
            
            $this->task->refresh();
            session()->flash('success', __('app.file_deleted_success'));
        }
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);
        
        $this->task->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $this->newComment,
        ]);
        
                    // Log comment addition
            $this->task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'comment_added',
                'new_value' => '',
                'details' => '',
            ]);
        
        // Update task's updated_at timestamp
        $this->task->touch();
        
        $this->task->refresh();
        $this->newComment = '';
        session()->flash('success', __('app.comment_added_success'));
    }

    public function deleteComment($commentId)
    {
        $comment = $this->task->comments()->where('id', $commentId)->first();
        if ($comment) {
            $comment->delete();
            $this->task->refresh();
            
            // Log comment deletion
            $this->task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'comment_deleted',
                'old_value' => '',
                'details' => '',
            ]);
            
            session()->flash('success', __('app.comment_deleted_success'));
        }
    }

    public function toggleEditMode()
    {
        $this->editMode = !$this->editMode;
        
        if ($this->editMode) {
            // Edit moduna geçerken mevcut değerleri yükle
            $this->title = $this->task->title;
            $this->description = $this->task->description;
            $this->type = $this->task->type;
            $this->status = $this->task->status;
            $this->deadline = $this->task->deadline;
            
            // Assigned users'ı yeniden yükle
            $this->loadAssignedUsers();
        }
    }

    public function updatedDeadline()
    {
        if ($this->deadline) {
            // Custom validation for deadline
            $rules = ['deadline' => 'date|after_or_equal:today'];
            $messages = [
                'deadline.date' => __('app.deadline_date_invalid'),
                'deadline.after_or_equal' => __('app.deadline_after_or_equal_today'),
            ];
            
            $this->validateOnly('deadline', $rules, $messages);
        }
    }

    public function updatedSearchQuery()
    {
        // Arama sorgusu 2 karakterden azsa sonuçları temizle
        if (strlen($this->searchQuery) < 2) {
            $this->searchResults = collect();
            return;
        }
        
        $assignedUserIds = [];
        foreach ($this->assignedUsers as $user) {
            $assignedUserIds[] = $user->id;
        }
        
        $this->searchResults = User::whereIn('role', ['personel', 'admin'])
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('surname', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('title', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('department', 'like', '%' . $this->searchQuery . '%');
            })
            ->whereNotIn('id', $assignedUserIds)
            ->limit(5)
            ->get();
            

    }

    public function selectFirstUser()
    {
        if ($this->searchResults && $this->searchResults->count() > 0) {
            $firstUser = $this->searchResults->first();
            if ($firstUser && isset($firstUser->id)) {
                $this->assignUser($firstUser->id);
            }
        }
    }

    public function assignUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            // Kullanıcının zaten atanmış olup olmadığını kontrol et
            $alreadyAssigned = false;
            foreach ($this->assignedUsers as $assignedUser) {
                if ($assignedUser->id == $userId) {
                    $alreadyAssigned = true;
                    break;
                }
            }
            
            if (!$alreadyAssigned) {
                $this->assignedUsers[] = $user;
                $this->searchQuery = '';
                $this->searchResults = collect();
                
                // Log will be created when save button is pressed
            }
        }
    }

    public function removeAssignedUser($userId)
    {
        $userToRemove = null;
        $newAssignedUsers = [];
        
        // Kullanıcıyı bul ve çıkar
        foreach ($this->assignedUsers as $user) {
            if ($user->id == $userId) {
                $userToRemove = $user;
            } else {
                $newAssignedUsers[] = $user;
            }
        }
        
        if ($userToRemove) {
            $this->assignedUsers = $newAssignedUsers;
            
            // Log will be created when save button is pressed
        }
    }

    public function deleteTask()
    {
        // Delete task files first
        foreach ($this->task->files as $file) {
            if (file_exists(storage_path('app/public/' . $file->file_path))) {
                unlink(storage_path('app/public/' . $file->file_path));
            }
            $file->delete();
        }
        
        // Delete task comments
        $this->task->comments()->delete();
        
        // Delete task logs
        $this->task->logs()->delete();
        
        // Delete task participants
        $this->task->participants()->detach();
        
        // Delete the task itself
        $this->task->delete();
        
        session()->flash('success', __('app.task_deleted_success'));
        
        return redirect()->route('admin.tasks');
    }



    private function logUserChanges($oldAssignedUserIds, $oldParticipantIds, $newAssignedUserIds, $newParticipantIds)
    {
        // Get old user IDs
        $oldAllUserIds = array_merge($oldAssignedUserIds, $oldParticipantIds);
        
        // Get new user IDs
        $newAllUserIds = array_merge($newAssignedUserIds, $newParticipantIds);
        
        // Find added users (only participants, main assigned user is handled separately)
        $addedUserIds = array_diff($newParticipantIds, $oldParticipantIds);
        foreach ($addedUserIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $this->task->logs()->create([
                    'user_id' => auth()->id(),
                    'action' => 'participant_added',
                    'details' => "{$user->name} {$user->surname}",
                ]);
            }
        }
        
        // Find removed users (including main assigned user)
        $removedUserIds = array_diff($oldAllUserIds, $newAllUserIds);
        foreach ($removedUserIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $this->task->logs()->create([
                    'user_id' => auth()->id(),
                    'action' => 'user_removed',
                    'details' => "{$user->name} {$user->surname}",
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.task-detail', [
            'task' => $this->task,
            'users' => $this->users,
            'comments' => $this->task->comments()->with('user')->latest()->get(),
            'logs' => $this->task->logs()->with('user')->latest()->take(10)->get(), // Sadece son 10 log
        ])->layout('layouts.admin');
    }
} 