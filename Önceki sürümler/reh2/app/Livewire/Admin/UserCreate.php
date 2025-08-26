<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class UserCreate extends Component
{
    use WithFileUploads;

    public $name, $surname, $email, $password, $title, $department, $phone, $role;
    public $success = false;
    public $photo;

    public function save()
    {
        $this->validate([
            'name' => 'required|string',
            'surname' => 'nullable|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'title' => 'nullable|string',
            'department' => 'nullable|string',
            'phone' => 'nullable|string',
            'role' => 'required|string|in:admin,personel',
            'photo' => 'nullable|image|max:1024', // max 1MB
        ]);

        $data = [
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'title' => $this->title,
            'department' => $this->department,
            'phone' => $this->phone,
            'role' => $this->role,
        ];

        if ($this->photo) {
            $path = $this->photo->store('photos', 'public');
            $data['photo'] = $path;
        }

        User::create($data);

        $this->reset(['name', 'surname', 'email', 'password', 'title', 'department', 'phone', 'role', 'photo']);
        $this->success = true;
    }

    public function render()
    {
        return view('livewire.admin.user-create', [
        ])->layout('layouts.admin');// importante Yeni kullanıcı ekle viewı burdan alıyor
    }
}
