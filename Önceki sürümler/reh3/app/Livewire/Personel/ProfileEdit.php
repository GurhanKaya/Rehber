<?php

namespace App\Livewire\Personel;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

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
            'photo' => [
                'nullable', 
                'image', 
                'max:2048', // 2MB max
                'mimes:jpeg,jpg,png,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ],
        ];
    }

    protected $messages = [
        'name.regex' => 'Ad alanı sadece harf, boşluk, tire ve nokta içerebilir.',
        'surname.regex' => 'Soyad alanı sadece harf, boşluk, tire ve nokta içerebilir.',
        'email.email' => 'Geçerli bir e-posta adresi giriniz.',
        'email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
        'password.min' => 'Şifre en az 8 karakter olmalıdır.',
        'phone.regex' => 'Geçerli bir telefon numarası giriniz.',
        'photo.max' => 'Fotoğraf boyutu en fazla 2MB olabilir.',
        'photo.mimes' => 'Fotoğraf formatı JPEG, JPG, PNG veya WebP olmalıdır.',
        'photo.dimensions' => 'Fotoğraf boyutları 100x100 ile 2000x2000 piksel arasında olmalıdır.',
    ];

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
                $this->addError('photo', 'Geçersiz dosya türü.');
                return;
            }

            // Delete old photo if exists
            if ($this->user->photo && Storage::disk('public')->exists($this->user->photo)) {
                Storage::disk('public')->delete($this->user->photo);
            }

            // Generate secure filename
            $extension = $this->photo->getClientOriginalExtension();
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $path = $this->photo->storeAs('photos', $filename, 'public');
            $data['photo'] = $path;
        }

        try {
            $this->user->update($data);
            session()->flash('success', 'Bilgileriniz güncellendi.');
            return redirect()->route('personel.home');
        } catch (\Exception $e) {
            $this->addError('general', 'Güncelleme sırasında bir hata oluştu.');
            logger()->error('Profile update failed: ' . $e->getMessage());
        }
    }

    public function deletePhoto()
    {
        try {
            if ($this->user->photo && Storage::disk('public')->exists($this->user->photo)) {
                Storage::disk('public')->delete($this->user->photo);
                $this->user->update(['photo' => null]);
                session()->flash('success', 'Fotoğraf silindi.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Fotoğraf silinirken bir hata oluştu.');
            logger()->error('Photo deletion failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.personel.profile-edit')->layout('layouts.personel');
    }
} 