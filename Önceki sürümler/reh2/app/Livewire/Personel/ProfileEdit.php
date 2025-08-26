<?php

namespace App\Livewire\Personel;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ProfileEdit extends Component
{
    use WithFileUploads;

    public User $user;
    public $name, $surname, $email, $password, $title, $department, $phone;
    public $photo;

    public function mount()
    {
        $this->user = auth()->user();
        $this->name = $this->user->name;
        $this->surname = $this->user->surname;
        $this->email = $this->user->email;
        $this->title = $this->user->title;
        $this->department = $this->user->department;
        $this->phone = $this->user->phone;
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
            'photo' => 'nullable|image|max:1024', // max 1MB
        ]);

        $data = [
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'title' => $this->title,
            'department' => $this->department,
            'phone' => $this->phone,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
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

        session()->flash('success', 'Bilgileriniz güncellendi.');
        return redirect()->route('personel.home');
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
        return view('livewire.personel.profile-edit')->layout('layouts.personel');
    }
} 