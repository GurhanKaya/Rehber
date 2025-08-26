<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class UserEdit extends Component
{
    use WithFileUploads;

    public User $user;
    public $userId;
    public $name, $surname, $email, $password, $title, $department, $phone, $role;
    public $photo;

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
            'role' => 'required|string|in:admin,personel',
            'photo' => 'nullable|image|max:1024', // max 1MB
        ]);

        $data = [
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'title' => $this->title,
            'department' => $this->department,
            'phone' => $this->phone,
            'role' => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        } else {
            $data['password'] = $this->user->password;
        }

        if ($this->photo) {
            // Delete old photo if exists
            if ($this->user->photo && Storage::disk('public')->exists($this->user->photo)) {
                Storage::disk('public')->delete($this->user->photo);
            }
            // Store new photo
            $path = $this->photo->store('photos', 'public');
            $data['photo'] = $path;
        }

        $this->user->update($data);

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

    public function deletePhoto()
    {
        if ($this->user->photo && Storage::disk('public')->exists($this->user->photo)) {
            Storage::disk('public')->delete($this->user->photo);
            $this->user->update(['photo' => null]);
            session()->flash('success', 'Fotoğraf silindi.');
        }
    }

    public function render()
    {
        return view('livewire.admin.user-edit', [
        ])->layout('layouts.admin');// importante kullanıcı edit viewı burdan alıyor
    }
}
