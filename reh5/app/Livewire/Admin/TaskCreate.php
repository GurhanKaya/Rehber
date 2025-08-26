<?php

namespace App\Livewire\Admin;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TaskCreate extends Component
{
    use WithFileUploads;

    public $layout = 'layouts.admin';

    public $title;
    public $description;
    public $type = 'public';
    public $status = 'bekliyor';
    public $assigned_user_id;
    public $deadline;
    public $uploadedFiles = [];
    public $savedFiles = [];
    public $newFiles = [];

    protected function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:public,private,cooperative',
            'status' => 'required|in:bekliyor,devam ediyor,tamamlandı,iptal',
            'assigned_user_id' => 'nullable|exists:users,id',
            'deadline' => 'nullable|date|after_or_equal:today',
            'uploadedFiles.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt,xlsx'
        ];

        // Özel görevler için personel ataması zorunlu
        if ($this->type === 'private') {
            $rules['assigned_user_id'] = 'required|exists:users,id';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'title.required' => __('app.title_required'),
            'title.max' => __('app.title_max'),
            'type.required' => __('app.type_required'),
            'type.in' => __('app.type_invalid'),
            'status.required' => __('app.status_required'),
            'status.in' => __('app.status_invalid'),
            'assigned_user_id.required' => __('app.assigned_user_required'),
            'assigned_user_id.exists' => __('app.assigned_user_not_found'),
            'deadline.date' => __('app.deadline_date_invalid'),
            'deadline.after_or_equal' => __('app.deadline_after_or_equal_today'),
            'uploadedFiles.*.file' => __('app.file_invalid'),
            'uploadedFiles.*.max' => __('app.file_max_size'),
            'uploadedFiles.*.mimes' => __('app.file_unsupported_format'),
        ];
    }

    public function mount()
    {
        $this->uploadedFiles = [];
        $this->savedFiles = [];
        $this->newFiles = [];
    }

    public function updatedNewFiles()
    {
        $this->validate([
            'newFiles.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt,xlsx'
        ], [
            'newFiles.*.file' => __('app.file_invalid'),
            'newFiles.*.max' => __('app.file_max_size'),
            'newFiles.*.mimes' => __('app.file_unsupported_format'),
        ]);

        // Mevcut kaydedilmiş dosya adlarını (orijinal ad) küçük harfe çevirerek bir kümede tut
        $existingOriginalNames = collect($this->savedFiles)
            ->pluck('original_name')
            ->filter()
            ->map(fn($n) => mb_strtolower($n))
            ->values()
            ->all();

        // Aynı yükleme içinde tekrarı engellemek için yeni görülen adları tut
        $seenInThisBatch = [];

        foreach ($this->newFiles as $file) {
            if ($file) {
                $originalName = $file->getClientOriginalName();
                $originalNameLower = mb_strtolower($originalName);

                // Daha önce eklenmişse veya bu partide tekrar gelmişse atla ve kullanıcıyı bilgilendir
                if (in_array($originalNameLower, $existingOriginalNames, true) || in_array($originalNameLower, $seenInThisBatch, true)) {
                    session()->flash('error', __('app.duplicate_file_exists'));
                    continue;
                }

                // Dosyaları hemen kaydet
                $fileName = time() . '_' . uniqid() . '_' . $originalName;
                $filePath = $file->storeAs('temp-task-files', $fileName, 'public');
                
                $this->savedFiles[] = [
                    'id' => uniqid(),
                    'original_name' => $originalName,
                    'stored_name' => $fileName,
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'temp' => true
                ];

                // Bu yükleme içindeki görülenlere ekle
                $seenInThisBatch[] = $originalNameLower;
            }
        }
        
        // Yeni dosyaları temizle
        $this->newFiles = [];
    }

    public function removeFile($index)
    {
        if (isset($this->uploadedFiles[$index])) {
            unset($this->uploadedFiles[$index]);
            $this->uploadedFiles = array_values($this->uploadedFiles);
        }
    }

    public function removeSavedFile($fileId)
    {
        foreach ($this->savedFiles as $index => $file) {
            if ($file['id'] === $fileId) {
                // Geçici dosyayı sil
                if (isset($file['temp']) && $file['temp'] && Storage::disk('public')->exists($file['file_path'])) {
                    Storage::disk('public')->delete($file['file_path']);
                }
                unset($this->savedFiles[$index]);
                $this->savedFiles = array_values($this->savedFiles);
                break;
            }
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

    public function saveTask()
    {
        $this->validate($this->rules(), $this->messages());

        try {
            $task = Task::create([
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
                'status' => $this->status,
                'assigned_user_id' => $this->assigned_user_id,
                'deadline' => $this->deadline,
                'created_by' => Auth::id(),
            ]);

            // Kaydedilmiş dosyaları görevle ilişkilendir
            if ($this->savedFiles) {
                foreach ($this->savedFiles as $fileData) {
                    // Geçici dosyayı kalıcı klasöre taşı
                    $newPath = 'task-files/' . $fileData['stored_name'];
                    Storage::disk('public')->move($fileData['file_path'], $newPath);
                    
                    TaskFile::create([
                        'task_id' => $task->id,
                        'file_name' => $fileData['original_name'],
                        'file_path' => $newPath,
                        'file_size' => $fileData['file_size'],
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            // Eski uploadedFiles sistemi de çalışsın
            if ($this->uploadedFiles) {
                foreach ($this->uploadedFiles as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('task-files', $fileName, 'public');
                    
                    TaskFile::create([
                        'task_id' => $task->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_size' => $file->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            // Log task creation
            $task->logs()->create([
                'user_id' => Auth::id(),
                'action' => 'task_created',
                'details' => '',
            ]);

            // Eğer görev birine atandıysa, atama log kaydı ekle
            if ($this->assigned_user_id) {
                $assignedUser = User::find($this->assigned_user_id);
                if ($assignedUser) {
                    $task->logs()->create([
                        'user_id' => Auth::id(),
                        'action' => 'user_assigned',
                        'details' => "{$assignedUser->name} {$assignedUser->surname}",
                    ]);
                }
            }

            // Eğer dosya yüklendiyse, dosya yükleme log kaydı ekle
            $totalFiles = count($this->savedFiles) + count($this->uploadedFiles);
            if ($totalFiles > 0) {
                $uploadedFileNames = [];
                
                // savedFiles'dan dosya adlarını topla
                foreach ($this->savedFiles as $fileData) {
                    $uploadedFileNames[] = $fileData['original_name'];
                }
                
                // uploadedFiles'dan dosya adlarını topla
                foreach ($this->uploadedFiles as $file) {
                    $uploadedFileNames[] = $file->getClientOriginalName();
                }
                
                $task->logs()->create([
                    'user_id' => Auth::id(),
                    'action' => 'files_uploaded',
                    'details' => implode(', ', $uploadedFileNames),
                ]);
            }

            session()->flash('success', __('app.task_created_success'));
            return redirect()->route('admin.tasks');

        } catch (\Exception $e) {
            session()->flash('error', __('app.task_creation_error') . ': ' . $e->getMessage());
        }
    }

    public function render()
    {
        $users = User::where('role', 'personel')->orderBy('name')->get();
        
        return view('livewire.admin.task-create', [
            'users' => $users
        ])->layout('layouts.admin');
    }
} 