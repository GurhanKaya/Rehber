<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserEdit extends Component
{
    public User $user;
    public $userId;
    public $name, $surname, $email, $password, $title, $department, $phone, $role;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->surname = $user->surname;
        $this->email = $user->email;
        $this->title = $user->title;
        $this->department = $user->department;
        $this->phone = $user->phone;
        $this->role = $user->role;
        $this->userId = $user->id;//delete için burda
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string',
            'surname' => 'nullable|string',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:6',
            'title' => 'nullable|string',
            'department' => 'nullable|string',
            'phone' => 'nullable|string',
            'role' => 'required|string',
        ]);

        $this->user->update([
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'password' => $this->password ? Hash::make($this->password) : $this->user->password,
            'title' => $this->title,
            'department' => $this->department,
            'phone' => $this->phone,
            'role' => $this->role,
        ]);

        session()->flash('success', 'Kullanıcı güncellendi.');
        return redirect()->route('admin.users');
    }

    public function delete()
    {
        $user = User::findOrFail($this->userId);
        $user->delete();

        session()->flash('success', 'Kullanıcı başarıyla silindi.');
        return redirect()->route('admin.users');
    }

    public function render()
    {
        return view('livewire.admin.user-edit', [
        ])->layout('layouts.admin');// importante kullanıcı edit viewı burdan alıyor
    }
}
