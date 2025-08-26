<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Facades\Storage;

trait WithImageUpload
{
    protected array $defaultAllowedImageMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

    protected function ensureAllowedImage($file, ?array $allowedMimes = null): bool
    {
        $allowed = $allowedMimes ?: $this->defaultAllowedImageMimes;
        $mime = $file->getMimeType();
        if (!in_array($mime, $allowed, true)) {
            $this->addError('photo', __('validation.mimes', ['attribute' => 'photo']));
            return false;
        }
        return true;
    }

    protected function generateSafeFilename(string $extension): string
    {
        return uniqid('', true) . '_' . time() . '.' . $extension;
    }

    protected function storeImage($file, array $options = []): string
    {
        $disk = $options['disk'] ?? 'public';
        $dir = $options['dir'] ?? 'photos';
        $extension = $file->getClientOriginalExtension();
        $filename = $this->generateSafeFilename($extension);
        return $file->storeAs($dir, $filename, $disk);
    }

    protected function deleteIfExists(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
