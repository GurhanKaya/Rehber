<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Livewire\Traits\WithUserValidation;
use App\Notifications\WelcomeUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class UserCreateSimplified extends BaseAdminComponent
{
    use WithFileUploads, WithUserValidation;

    public $name = '';
    public $surname = '';
    public $email = '';
    public $password = '';
    public $title = '';
    public $department = '';
    public $phone = '';
    public $role = 'personel';
    public $photo;
    public $success = false;

    protected function getViewName(): string
    {
        return 'livewire.admin.user-create';
    }

    public function rules()
    {
        return $this->getUserValidationRules();
    }

    public function messages()
    {
        return $this->getUserValidationMessages();
    }

    public function save()
    {
        $this->validate();

        try {
            $userData = $this->prepareUserData();
            
            $user = User::create($userData);
            
            $this->handlePhotoUpload($user);
            $this->sendWelcomeNotification($user);
            
            $this->success = true;
            $this->resetForm();
            
            session()->flash('success', 'Kullanıcı başarıyla oluşturuldu! E-mail doğrulaması otomatik olarak yapıldı ve hoş geldin e-postası gönderildi.');
            
        } catch (\Exception $e) {
            $this->addError('general', 'Kullanıcı oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset([
            'name', 'surname', 'email', 'password', 'title', 
            'department', 'phone', 'role', 'photo'
        ]);
        $this->role = 'personel';
    }

    private function prepareUserData(): array
    {
        return [
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'title' => $this->title,
            'department' => $this->department,
            'phone' => $this->phone,
            'role' => $this->role,
            'email_verified_at' => now(),
        ];
    }

    private function handlePhotoUpload(User $user)
    {
        if ($this->photo) {
            $path = $this->photo->store('photos', 'public');
            $user->update(['photo' => $path]);
        }
    }

    private function sendWelcomeNotification(User $user)
    {
        try {
            $user->notify(new WelcomeUser($this->password));
        } catch (\Exception $e) {
            Log::warning('Welcome email could not be sent: ' . $e->getMessage());
        }
    }
} 