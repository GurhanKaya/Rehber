<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use App\Notifications\WelcomeUser;
use App\Livewire\Traits\WithImageUpload;
use App\Livewire\Traits\WithSanitizedInput;
use App\Livewire\Traits\WithUserList;
use Livewire\Attributes\Layout as LWLayout;

#[LWLayout('layouts.admin')]
class UserCreate extends Component
{
    use WithFileUploads;
    use WithImageUpload;
    use WithSanitizedInput;
    use WithUserList;

    public $name, $surname, $email, $password, $title_id, $department_id, $phone, $role;
    public $success = false;
    public $photo;

    public function mount()
    {
        // Load filter options for dropdowns
        $this->loadFilterOptions();
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],
            'surname' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],
            'email' => ['required', 'email:rfc,dns', 'unique:users,email', 'max:255'],
            'password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'title_id' => ['nullable', 'integer', 'exists:titles,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'phone' => ['nullable', 'string', 'regex:/^[\+]?[-0-9\s\(\)]{10,20}$/'],
            'role' => ['required', 'string', 'in:admin,personel'],
            'photo' => [
                'nullable', 
                'image', 
                'max:2048',
                'mimes:jpeg,jpg,png,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('app.email_already_used'),
        ];
    }

    public function save()
    {
        try {
            $this->validate();

            $data = [
                'name' => strip_tags(trim($this->name)),
                'surname' => $this->sanitizeNullableString($this->surname),
                'email' => $this->sanitizeEmail($this->email),
                'password' => Hash::make($this->password),
                'title_id' => $this->title_id ?: null,
                'department_id' => $this->department_id ?: null,
                'phone' => $this->sanitizePhone($this->phone),
                'role' => $this->role,
                'email_verified_at' => now(),
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
            if (!$this->ensureAllowedImage($this->photo)) {
                return;
            }
            $path = $this->storeImage($this->photo, ['dir' => 'photos', 'disk' => 'public']);
            $data['photo'] = $path;
        }

        try {
            Log::info('UserCreate: Creating user with data', $data);
            $user = User::create($data);
            Log::info('UserCreate: User created successfully', ['user_id' => $user->id, 'email' => $user->email]);

            if ($user->email) {
                try {
                    $user->notify(new WelcomeUser($user));
                    Log::info('UserCreate: Welcome email sent successfully', ['user_id' => $user->id]);
                } catch (\Exception $emailError) {
                    Log::warning('UserCreate: Failed to send welcome email', [
                        'user_id' => $user->id, 
                        'error' => $emailError->getMessage()
                    ]);
                }
            }

            $this->reset(['name', 'surname', 'email', 'password', 'title_id', 'department_id', 'phone', 'role', 'photo']);
            $this->success = true;

            Log::info('UserCreate: Process completed successfully', ['user_id' => $user->id]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('UserCreate: Database error during user creation', [
                'error' => $e->getMessage(),
                'sql_state' => $e->errorInfo[0] ?? 'unknown'
            ]);

            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->addError('email', __('app.email_already_used'));
            } else {
                $this->addError('general', __('app.operation_failed') . ': ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('UserCreate: Unexpected error during user creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addError('general', __('app.operation_failed') . ': ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.user-create');
    }
}
