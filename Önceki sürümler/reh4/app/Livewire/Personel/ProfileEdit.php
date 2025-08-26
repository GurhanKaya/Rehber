<?php

namespace App\Livewire\Personel;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rules\Password;
use App\Livewire\Traits\WithUserList;
use Livewire\Attributes\Layout as LWLayout;

#[LWLayout('layouts.personel')]
class ProfileEdit extends Component
{
    use WithFileUploads;
    use WithUserList;

    public User $user;
    public $name, $surname, $email, $password, $title, $department, $phone, $locale;
    public $photo;
    public $currentPhotoPath;
    protected $messages = [];

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->surname = $this->user->surname;
        $this->email = $this->user->email;
        $this->title = $this->user->title;
        $this->department = $this->user->department;
        $this->phone = $this->user->phone;
        $this->locale = $this->user->locale ?? config('app.locale');
        $this->currentPhotoPath = $this->user->photo;

        // Load filter options for dropdowns
        $this->loadFilterOptions('personel');

        // Set validation messages
        $this->messages = [
            'name.regex' => __('app.name_regex_error'),
            'surname.regex' => __('app.surname_regex_error'),
            'email.email' => __('app.email_invalid'),
            'email.unique' => __('app.email_already_used'),
            'password.min' => __('app.password_min_error'),
            'phone.regex' => __('app.phone_invalid'),
            'photo.max' => __('app.photo_max_size_error'),
            'photo.mimes' => __('app.photo_format_error'),
            'photo.dimensions' => __('app.photo_dimensions_error'),
        ];
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],
            'surname' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],
            'email' => ['required', 'email:rfc,dns', 'unique:users,email,' . $this->user->id, 'max:255'],
            'password' => ['nullable', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^[\+]?[0-9\s\-\(\)]{10,20}$/'],
            'locale' => ['required', 'in:tr,en'],
            'photo' => [
                'nullable', 
                'image', 
                'max:2048', // 2MB max
                'mimes:jpeg,jpg,png,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ],
        ];
    }

    public function update()
    {
        $this->validate();

        $data = [
            'name' => strip_tags(trim($this->name)),
            'surname' => $this->surname ? strip_tags(trim($this->surname)) : null,
            'email' => strtolower(strip_tags(trim($this->email))),
            'title' => $this->title ? strip_tags(trim($this->title)) : null,
            'department' => $this->department ? strip_tags(trim($this->department)) : null,
            'phone' => $this->phone ? preg_replace('/[^0-9\+\-\(\)\s]/', '', $this->phone) : null,
            'locale' => $this->locale,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->photo) {
            // Additional security checks
            $mimeType = $this->photo->getMimeType();
            
            // Validate MIME type
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!in_array($mimeType, $allowedMimes)) {
                $this->addError('photo', __('app.invalid_file_type'));
                return;
            }

            // Delete old photo if exists
            if ($this->user->photo && Storage::disk('public')->exists($this->user->photo)) {
                Storage::disk('public')->delete($this->user->photo);
            }

            // Generate secure filename
            $extension = $this->photo->getClientOriginalExtension();
            $filename = uniqid('', true) . '_' . time() . '.' . $extension;
            $path = $this->photo->storeAs('photos', $filename, 'public');
            $data['photo'] = str_replace('\\', '/', $path);
        }

        try {
            $this->user->update($data);
            // Session ve uygulama dili güncelle
            session(['locale' => $this->locale]);
            app()->setLocale($this->locale);
            session()->flash('success', __('app.profile_updated'));
            return redirect()->route('personel.home');
        } catch (\Exception $e) {
            $this->addError('general', __('app.operation_failed'));
            logger()->error('Profile update failed: ' . $e->getMessage());
        }
    }

    public function deletePhoto()
    {
        try {
            if ($this->user->photo && Storage::disk('public')->exists($this->user->photo)) {
                Storage::disk('public')->delete($this->user->photo);
                $this->user->update(['photo' => null]);
                session()->flash('success', __('app.photo_deleted'));
            }
        } catch (\Exception $e) {
            session()->flash('error', __('app.operation_failed'));
            logger()->error('Photo deletion failed: ' . $e->getMessage());
        }
    }

    /**
     * Fotoğraf seçildiğinde otomatik kaydet
     */
    public function updatedPhoto(): void
    {
        $this->validateOnly('photo');

        if (!$this->photo) {
            return;
        }

        // MIME doğrulama
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($this->photo->getMimeType(), $allowedMimes)) {
            $this->addError('photo', __('app.invalid_file_type'));
            return;
        }

        // Eski fotoğrafı sil
        if ($this->user->photo && Storage::disk('public')->exists($this->user->photo)) {
            Storage::disk('public')->delete($this->user->photo);
        }

        // Yeni fotoğrafı kaydet
        $extension = $this->photo->getClientOriginalExtension();
        $filename = uniqid('', true) . '_' . time() . '.' . $extension;
        $path = $this->photo->storeAs('photos', $filename, 'public');
        $normalizedPath = str_replace('\\', '/', $path);
        $this->user->update(['photo' => $normalizedPath]);

        // State güncelle
        $this->user->refresh();
        $this->currentPhotoPath = $this->user->photo;
        $this->photo = null;

        // Debug için log
        Log::info('Personel photo updated - DETAILED DEBUG', [
            'stored_path' => $path,
            'normalized_path' => $normalizedPath,
            'currentPhotoPath' => $this->currentPhotoPath,
            'storage_url' => Storage::url($normalizedPath),
            'storage_exists' => Storage::disk('public')->exists($normalizedPath),
            'full_storage_path' => Storage::disk('public')->path($normalizedPath),
            'public_url' => url('storage/' . $normalizedPath),
            'user_id' => $this->user->id,
            'photo_field_value' => $this->user->getRawOriginal('photo')
        ]);

        session()->flash('success', __('app.profile_photo_updated'));
    }

    public function render()
    {
        return view('livewire.personel.profile-edit');
    }
} 