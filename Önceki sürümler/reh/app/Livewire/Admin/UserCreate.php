<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserCreate extends Component
{
    public $name, $surname, $email, $password, $title, $department, $phone, $role;
    public $success = false;

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
            'role' => 'required|string|in:admin,user',
        ]);

        User::create([
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'title' => $this->title,
            'department' => $this->department,
            'phone' => $this->phone,
            'role' => $this->role,
        ]);

        $this->reset(['name', 'surname', 'email', 'password', 'title', 'department', 'phone', 'role']);
        $this->success = true;
    }

    public function render()
    {
        return view('livewire.admin.user-create', [
        ])->layout('layouts.admin');// importante Yeni kullanıcı ekle viewı burdan alıyor
    }
}
