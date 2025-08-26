<?php

namespace App\Livewire\Personel;

use Livewire\Component;
use App\Models\Task;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class TaskDetail extends Component
{
    use WithFileUploads;

    public $task;
    public $newStatus;
    public $newFiles = [];
    public $uploadedFiles = [];
    public $newComment = '';
    public $isParticipant = false;
    public $isAssigned = false;
    public $fileUploadError = '';
    public $showAllParticipants = false;

    public function mount(Task $task)
    {
        $user = auth()->user();
        
        // Check if user is assigned to the task
        $this->isAssigned = $task->assigned_user_id === $user->id;
        
        // Check if user is a participant in cooperative task
        $this->isParticipant = $task->participants()->where('user_id', $user->id)->exists();
        
        // Allow access if user is assigned OR is a participant in cooperative task
        abort_unless($this->isAssigned || $this->isParticipant, 403, 'Bu görevi görüntüleme yetkiniz yok.');
        
        $this->task = $task->load(['files', 'assignedUser', 'participants', 'creator', 'comments.user']);
        $this->newStatus = $this->task->status;
    }

    public function updatedNewFiles()
    {
        $this->fileUploadError = '';
        $duplicateFiles = [];
        
        foreach ($this->newFiles as $file) {
            // Check for duplicate file names in current task
            $existingFileNames = $this->task->files()->pluck('file_name')->toArray();
            
            if (in_array($file->getClientOriginalName(), $existingFileNames)) {
                $duplicateFiles[] = $file->getClientOriginalName();
                continue;
            }
            
            // Check for duplicate file names in currently uploaded files
            $currentFileNames = collect($this->uploadedFiles)->map(function($uploadedFile) {
                return method_exists($uploadedFile, 'getClientOriginalName') ? 
                       $uploadedFile->getClientOriginalName() : $uploadedFile;
            })->toArray();
            
            if (in_array($file->getClientOriginalName(), $currentFileNames)) {
                $duplicateFiles[] = $file->getClientOriginalName();
                continue;
            }
            
            $this->uploadedFiles[] = $file;
        }
        
        if (!empty($duplicateFiles)) {
            $this->fileUploadError = 'Aynı isimli dosyalar: ' . implode(', ', $duplicateFiles);
        }
        
        $this->newFiles = [];
    }

    public function removeUploadedFile($index)
    {
        array_splice($this->uploadedFiles, $index, 1);
        $this->fileUploadError = '';
    }

    public function toggleParticipants()
    {
        $this->showAllParticipants = !$this->showAllParticipants;
    }

    public function updateStatus()
    {
        // For public tasks, any personnel can update if not assigned to anyone
        // For other tasks, only assigned user can update status
        if ($this->task->type === 'public' && !$this->task->assigned_user_id) {
            // Public task without assignment - any personnel can update
        } elseif (!$this->isAssigned) {
            session()->flash('error', 'Sadece atanmış kullanıcı durumu güncelleyebilir.');
            return;
        }

        if (in_array($this->newStatus, ['bekliyor', 'devam ediyor', 'tamamlandı', 'iptal'])) {
            $this->task->status = $this->newStatus;
            $this->task->save();
            
            // Add activity log
            $this->task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'status_updated',
                'old_value' => $this->task->getOriginal('status'),
                'new_value' => $this->newStatus,
            ]);
            
            session()->flash('success', 'Görev durumu güncellendi.');
        }
    }

    public function leaveTask()
    {
        $user = auth()->user();
        
        // Check if user is a participant in cooperative task
        if ($this->task->type === 'cooperative') {
            // Check if user is actually a participant
            $isParticipant = $this->task->participants()->where('user_id', $user->id)->exists();
            
            if ($isParticipant) {
                // Remove user from participants
                $this->task->participants()->detach($user->id);
                
                // Add activity log
                $this->task->logs()->create([
                    'user_id' => auth()->id(),
                    'action' => 'user_left_task',
                    'details' => "Kullanıcı görevden ayrıldı: {$user->name} {$user->surname}",
                ]);
                
                session()->flash('success', 'Görevden başarıyla ayrıldınız.');
                
                // Redirect to task list
                return redirect()->route('personel.tasks');
            } else {
                session()->flash('error', 'Bu göreve katılımcı değilsiniz.');
            }
        } else {
            session()->flash('error', 'Bu görevden ayrılma yetkiniz yok.');
        }
    }

    public function uploadFiles()
    {
        // Both assigned user and participants can upload files
        if (!$this->isAssigned && !$this->isParticipant) {
            session()->flash('error', 'Dosya yükleme yetkiniz yok.');
            return;
        }

        if (empty($this->uploadedFiles)) {
            $this->fileUploadError = 'Yüklenecek dosya seçiniz.';
            return;
        }

        $this->fileUploadError = '';

        $this->validate([
            'uploadedFiles.*' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,txt', // max 10MB
        ]);

        // Get existing file names to check for duplicates
        $existingFileNames = $this->task->files()->pluck('file_name')->toArray();
        $duplicateNames = [];
        $uploadedCount = 0;

        foreach ($this->uploadedFiles as $file) {
            $originalName = $file->getClientOriginalName();
            
            // Check if file with same name already exists in the task
            if (in_array($originalName, $existingFileNames)) {
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

            // Add to existing file names array to prevent duplicates in the same upload session
            $existingFileNames[] = $originalName;
            $uploadedCount++;
        }

        // Add activity log
        if ($uploadedCount > 0) {
            $this->task->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'files_uploaded',
                'new_value' => "{$uploadedCount} dosya yüklendi",
            ]);
        }

        $this->task->refresh();
        $this->uploadedFiles = [];

        if ($duplicateNames) {
            $this->fileUploadError = 'Aynı isimli dosyalar yüklenmedi: ' . implode(', ', $duplicateNames);
        }
        
        if ($uploadedCount > 0) {
            session()->flash('success', "{$uploadedCount} dosya başarıyla yüklendi.");
            $this->fileUploadError = '';
        } elseif (empty($duplicateNames)) {
            $this->fileUploadError = 'Hiçbir dosya yüklenemedi.';
        }
    }

    public function deleteFile($fileId)
    {
        $file = $this->task->files()->where('id', $fileId)->first();
        
        if (!$file) {
            session()->flash('error', 'Dosya bulunamadı.');
            return;
        }

        // Only the uploader or assigned user can delete files
        if ($file->user_id != auth()->id() && !$this->isAssigned) {
            session()->flash('error', 'Bu dosyayı silme yetkiniz yok.');
            return;
        }

        // Delete file from storage
        if (Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        } elseif (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // Add activity log
        $this->task->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'file_deleted',
            'old_value' => $file->file_name,
        ]);

        $file->delete();
        $this->task->refresh();
        session()->flash('success', 'Dosya silindi.');
    }

    public function addComment()
    {
        // Both assigned user and participants can add comments
        if (!$this->isAssigned && !$this->isParticipant) {
            session()->flash('error', 'Yorum yapma yetkiniz yok.');
            return;
        }

        $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);

        $this->task->comments()->create([
            'user_id' => auth()->id(),
            'comment' => strip_tags(trim($this->newComment)),
        ]);

        // Add activity log
        $this->task->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'comment_added',
            'new_value' => 'Yorum eklendi',
        ]);

        $this->task->refresh();
        $this->newComment = '';
        session()->flash('success', 'Yorum eklendi.');
    }

    public function deleteComment($commentId)
    {
        $comment = $this->task->comments()->where('id', $commentId)->first();
        
        if (!$comment) {
            session()->flash('error', 'Yorum bulunamadı.');
            return;
        }

        // Only comment owner or assigned user can delete comments
        if ($comment->user_id != auth()->id() && !$this->isAssigned) {
            session()->flash('error', 'Bu yorumu silme yetkiniz yok.');
            return;
        }

        // Add activity log
        $this->task->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'comment_deleted',
            'old_value' => 'Yorum silindi',
        ]);

        $comment->delete();
        $this->task->refresh();
        session()->flash('success', 'Yorum silindi.');
    }

    public function render()
    {
        return view('livewire.personel.task-detail', [
            'task' => $this->task,
            'comments' => $this->task->comments()->with('user')->latest()->get(),
            'logs' => $this->task->logs()->with('user')->latest()->take(10)->get(),
            'isParticipant' => $this->isParticipant,
            'isAssigned' => $this->isAssigned,
        ])->layout('layouts.personel');
    }
} 