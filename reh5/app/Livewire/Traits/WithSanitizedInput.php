<?php

namespace App\Livewire\Traits;

trait WithSanitizedInput
{
    protected function sanitizeNullableString(?string $value): ?string
    {
        return $value !== null && $value !== '' ? strip_tags(trim($value)) : null;
    }

    protected function sanitizeEmail(string $email): string
    {
        return strtolower(strip_tags(trim($email)));
    }

    protected function sanitizePhone(?string $phone): ?string
    {
        if ($phone === null || $phone === '') {
            return null;
        }
        return preg_replace('/[^0-9\+\-\(\)\s]/', '', $phone);
    }
}
