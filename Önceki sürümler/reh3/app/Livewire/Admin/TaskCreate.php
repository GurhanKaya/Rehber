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
            'deadline' => 'nullable|date|after:now',
            'uploadedFiles.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt,xlsx'
        ];

        // Özel görevler için personel ataması zorunlu
        if ($this->type === 'private') {
            $rules['assigned_user_id'] = 'required|exists:users,id';
        }

        return $rules;
    }

    protected $messages = [
        'title.required' => 'Görev başlığı gereklidir.',
        'title.max' => 'Görev başlığı en fazla 255 karakter olabilir.',
        'type.required' => 'Görev türü seçilmelidir.',
        'type.in' => 'Geçersiz görev türü.',
        'status.required' => 'Durum seçilmelidir.',
        'status.in' => 'Geçersiz durum.',
        'assigned_user_id.required' => 'Özel görevler için personel ataması zorunludur.',
        'assigned_user_id.exists' => 'Seçilen personel bulunamadı.',
        'deadline.date' => 'Geçerli bir tarih giriniz.',
        'deadline.after' => 'Son tarih bugünden sonra olmalıdır.',
        'uploadedFiles.*.file' => 'Yüklenen dosya geçerli değil.',
        'uploadedFiles.*.max' => 'Dosya boyutu en fazla 10MB olabilir.',
        'uploadedFiles.*.mimes' => 'Desteklenmeyen dosya formatı.',
    ];

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
        ]);

        foreach ($this->newFiles as $file) {
            if ($file) {
                // Dosyaları hemen kaydet
                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('temp-task-files', $fileName, 'public');
                
                $this->savedFiles[] = [
                    'id' => uniqid(),
                    'original_name' => $file->getClientOriginalName(),
                    'stored_name' => $fileName,
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'temp' => true
                ];
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

    public function saveTask()
    {
        $this->validate();

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
                'details' => 'Görev oluşturuldu',
            ]);

            session()->flash('success', 'Görev başarıyla oluşturuldu!');
            return redirect()->route('admin.tasks');

        } catch (\Exception $e) {
            session()->flash('error', 'Görev oluşturulurken bir hata oluştu: ' . $e->getMessage());
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