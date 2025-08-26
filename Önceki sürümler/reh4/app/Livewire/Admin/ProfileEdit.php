<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rules\Password as PasswordRule;
use App\Livewire\Traits\WithImageUpload;
use App\Livewire\Traits\WithSanitizedInput;
use App\Livewire\Traits\WithUserList;
use App\Models\User;
use Livewire\Attributes\Layout as LWLayout;

#[LWLayout('layouts.admin')]
class ProfileEdit extends Component
{
    use WithFileUploads;
    use WithImageUpload;
    use WithSanitizedInput;
    use WithUserList;

    public $name;
    public $surname;
    public $email;
    public $password;
    public $title;
    public $department;
    public $phone;
    public $locale;
    public $photo;
    public $currentPhotoPath;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->name = $user->name;
        $this->surname = $user->surname;
        $this->email = $user->email;
        $this->title = $user->title;
        $this->department = $user->department;
        $this->phone = $user->phone;
        $this->locale = $user->locale ?? config('app.locale');
        $this->currentPhotoPath = $user->photo;

        // Load filter options for dropdowns
        $this->loadFilterOptions();
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],
            'surname' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email,' . Auth::id()],
            'password' => ['nullable', 'string', PasswordRule::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^[\+]?[-0-9\s\(\)]{10,20}$/'],
            'locale' => ['required', 'in:tr,en'],
            'photo' => [
                'nullable',
                'image',
                'max:2048',
                'mimes:jpeg,jpg,png,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ],
        ];
    }

    public function update(): void
    {
        $this->validate();

        /** @var User $user */
        $user = Auth::user();
        $user->name = strip_tags(trim($this->name));
        $user->surname = $this->sanitizeNullableString($this->surname);
        $user->email = $this->sanitizeEmail($this->email);
        $user->title = $this->sanitizeNullableString($this->title);
        $user->department = $this->sanitizeNullableString($this->department);
        $user->phone = $this->sanitizePhone($this->phone);
        $user->locale = $this->locale;

        if (!empty($this->password)) {
            $user->password = Hash::make($this->password);
        }

        if ($this->photo) {
            if (!$this->ensureAllowedImage($this->photo)) {
                return;
            }

            $this->deleteIfExists($user->photo, 'public');
            $stored = $this->storeImage($this->photo, ['dir' => 'photos', 'disk' => 'public']);
            $user->photo = str_replace('\\', '/', $stored);
        }

        $user->save();

        // Refresh current user and reactive photo path
        $user->refresh();
        Auth::setUser($user);
        $this->currentPhotoPath = $user->photo;

        session(['locale' => $this->locale]);
        app()->setLocale($this->locale);

        session()->flash('success', __('app.profile_updated'));
    }

    public function deletePhoto(): void
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->photo) {
            $this->deleteIfExists($user->photo, 'public');
            $user->photo = null;
            $user->save();
            $user->refresh();
            Auth::setUser($user);
            $this->currentPhotoPath = null;
            session()->flash('success', __('app.photo_deleted'));
        }
    }

    /**
     * Fotoğraf seçildiğinde otomatik yükle ve kaydet
     */
    public function updatedPhoto(): void
    {
        // Yalnızca fotoğraf alanını doğrula
        $this->validateOnly('photo');

        if (!$this->photo) {
            return;
        }

        if (!$this->ensureAllowedImage($this->photo)) {
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        // Eski fotoğrafı sil
        $this->deleteIfExists($user->photo, 'public');

        // Yeni fotoğrafı kaydet
        $stored = $this->storeImage($this->photo, ['dir' => 'photos', 'disk' => 'public']);
        $user->photo = str_replace('\\', '/', $stored);
        $user->save();

        // State güncelle
        $user->refresh();
        Auth::setUser($user);
        $this->currentPhotoPath = $user->photo;
        $this->photo = null;

        session()->flash('success', __('app.profile_photo_updated'));
    }

    public function changeLocale(string $locale): void
    {
        if (!in_array($locale, ['tr', 'en'])) {
            return;
        }
        /** @var User $user */
        $user = Auth::user();
        $user->locale = $locale;
        $user->save();
        session(['locale' => $locale]);
        app()->setLocale($locale);
        $this->locale = $locale;
        session()->flash('success', __('app.language_updated'));
    }

    public function render()
    {
        return view('livewire.admin.profile-edit');
    }
}


