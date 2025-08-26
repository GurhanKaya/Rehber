<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Traits\WithImageUpload;
use App\Livewire\Traits\WithSanitizedInput;
use App\Livewire\Traits\WithUserList;
use Livewire\Attributes\Layout as LWLayout;

#[LWLayout('layouts.admin')]
class UserEdit extends Component
{
    use WithFileUploads;
    use WithImageUpload;
    use WithSanitizedInput;
    use WithUserList;

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
        $this->userId = $user->id;

        // Load filter options for dropdowns
        $this->loadFilterOptions();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:6',
            'title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|in:admin,personel',
            'photo' => 'nullable|image|max:2048|mimes:jpeg,jpg,png,webp',
        ]);

        $data = [
            'name' => strip_tags(trim($this->name)),
            'surname' => $this->sanitizeNullableString($this->surname),
            'email' => $this->email,
            'title' => $this->sanitizeNullableString($this->title),
            'department' => $this->sanitizeNullableString($this->department),
            'phone' => $this->sanitizePhone($this->phone),
            'role' => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->photo) {
            if (!$this->ensureAllowedImage($this->photo)) {
                return;
            }
            $this->deleteIfExists($this->user->photo, 'public');
            $data['photo'] = $this->storeImage($this->photo, ['dir' => 'photos', 'disk' => 'public']);
        }

        $this->user->update($data);

        session()->flash('success', __('app.user_updated'));
        return redirect()->route('admin.users');
    }

    public function delete()
    {
        $user = User::findOrFail($this->userId);
        $this->deleteIfExists($user->photo, 'public');
        $user->delete();

        session()->flash('success', __('app.successfully_deleted'));
        return redirect()->route('admin.users');
    }

    public function deletePhoto()
    {
        if ($this->user->photo) {
            $this->deleteIfExists($this->user->photo, 'public');
            $this->user->update(['photo' => null]);
            session()->flash('success', __('app.photo_deleted'));
        }
    }

    public function render()
    {
        return view('livewire.admin.user-edit', [
        ]);
    }
}
