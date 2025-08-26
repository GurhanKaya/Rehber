<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Notifications\WelcomeUser;

class UserCreate extends Component
{
    use WithFileUploads;

    public $name, $surname, $email, $password, $title, $department, $phone, $role;
    public $success = false;
    public $photo;

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],
            'surname' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],
            'email' => ['required', 'email:rfc,dns', 'unique:users,email', 'max:255'],
            'password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^[\+]?[0-9\s\-\(\)]{10,20}$/'],
            'role' => ['required', 'string', 'in:admin,personel'],
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

    public function save()
    {
        try {
            $this->validate();

            $data = [
                'name' => strip_tags(trim($this->name)),
                'surname' => $this->surname ? strip_tags(trim($this->surname)) : null,
                'email' => strtolower(strip_tags(trim($this->email))),
                'password' => Hash::make($this->password),
                'title' => $this->title ? strip_tags(trim($this->title)) : null,
                'department' => $this->department ? strip_tags(trim($this->department)) : null,
                'phone' => $this->phone ? preg_replace('/[^0-9\+\-\(\)\s]/', '', $this->phone) : null,
                'role' => $this->role,
                'email_verified_at' => now(), // Auto-verify email for admin-created users
            ];

            Log::info('UserCreate: Starting user creation', ['email' => $data['email'], 'role' => $data['role']]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('UserCreate: Validation failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('UserCreate: Unexpected error during validation', ['error' => $e->getMessage()]);
            $this->addError('general', 'Validation sırasında bir hata oluştu: ' . $e->getMessage());
            return;
        }

        if ($this->photo) {
            // Additional security checks
            $originalName = $this->photo->getClientOriginalName();
            $extension = $this->photo->getClientOriginalExtension();
            $mimeType = $this->photo->getMimeType();
            
            // Validate MIME type
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!in_array($mimeType, $allowedMimes)) {
                $this->addError('photo', 'Geçersiz dosya türü.');
                return;
            }

            // Generate secure filename
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $path = $this->photo->storeAs('photos', $filename, 'public');
            $data['photo'] = $path;
        }

        try {
            Log::info('UserCreate: Creating user with data', $data);
            
            $user = User::create($data);
            Log::info('UserCreate: User created successfully', ['user_id' => $user->id, 'email' => $user->email]);
            
            // Send welcome email with login credentials
            if ($user->email) {
                try {
                    $user->notify(new WelcomeUser($user));
                    Log::info('UserCreate: Welcome email sent successfully', ['user_id' => $user->id]);
                } catch (\Exception $emailError) {
                    Log::warning('UserCreate: Failed to send welcome email', [
                        'user_id' => $user->id, 
                        'error' => $emailError->getMessage()
                    ]);
                    // Continue anyway - user creation succeeded
                }
            }
            
            $this->reset(['name', 'surname', 'email', 'password', 'title', 'department', 'phone', 'role', 'photo']);
            $this->success = true;
            // Only set success property, remove session flash to avoid duplicate notifications
            
            Log::info('UserCreate: Process completed successfully', ['user_id' => $user->id]);
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('UserCreate: Database error during user creation', [
                'error' => $e->getMessage(),
                'sql_state' => $e->errorInfo[0] ?? 'unknown'
            ]);
            
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->addError('email', 'Bu e-posta adresi zaten kullanılıyor.');
            } else {
                $this->addError('general', 'Veritabanı hatası: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('UserCreate: Unexpected error during user creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addError('general', 'Kullanıcı oluşturulurken beklenmeyen bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.user-create')->layout('layouts.admin');
    }
}
