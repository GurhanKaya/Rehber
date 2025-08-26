<?php

namespace App\Livewire\Traits;

use Illuminate\Validation\Rules\Password;

trait WithUserValidation
{
    protected function getUserValidationRules($isEdit = false, $userId = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],
            'surname' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],
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

        // Email validation
        if ($isEdit && $userId) {
            $rules['email'] = ['required', 'email:rfc,dns', 'unique:users,email,' . $userId, 'max:255'];
        } else {
            $rules['email'] = ['required', 'email:rfc,dns', 'unique:users,email', 'max:255'];
        }

        // Password validation
        if ($isEdit) {
            $rules['password'] = ['nullable', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()];
        } else {
            $rules['password'] = ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()];
        }

        return $rules;
    }

    protected function getUserValidationMessages(): array
    {
        return [
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
    }
} 