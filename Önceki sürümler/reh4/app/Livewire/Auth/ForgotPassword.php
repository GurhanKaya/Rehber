<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ForgotPassword extends Component
{
    public string $email = '';

    public function mount()
    {
        // URL'den email parametresini al
        $emailFromUrl = request()->query('email');
        if ($emailFromUrl) {
            $this->email = $emailFromUrl;
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // Email gönderme işlemini optimize et
        $status = Password::sendResetLink($this->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', __('auth.reset_link_sent'));
        } else {
            // Güvenlik için aynı mesajı göster (kullanıcının varlığını açık etme)
            session()->flash('status', __('auth.reset_link_sent'));
        }
    }
}
