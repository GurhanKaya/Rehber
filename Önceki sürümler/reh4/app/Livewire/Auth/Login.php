<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;
    public bool $showForgotPasswordSuggestion = false;

    public function mount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $redirectRoute = $user->role === 'admin' ? 'admin.home' : 'personel.home';
            return redirect()->route($redirectRoute);
        }

        // Önceki yanlış giriş denemelerini kontrol et
        $this->checkPreviousFailedAttempts();
    }

    public function render()
    {
        return view('livewire.auth.login')->with('title', 'Giriş');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            // Yanlış giriş sayısını kontrol et
            $failedAttempts = RateLimiter::attempts($this->throttleKey());
            
            if ($failedAttempts >= 3) {
                // 3 veya daha fazla yanlış giriş varsa forgot password önerisi göster
                $this->showForgotPasswordSuggestion = true;
                session()->flash('forgot_password_suggestion', true);
            }

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $user = Auth::user();
        
        // Kullanıcının rolüne göre yönlendir
        $redirectRoute = $user->role === 'admin' ? 'admin.home' : 'personel.home';
        $this->redirectIntended(default: route($redirectRoute, absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    /**
     * Check previous failed attempts and show forgot password suggestion
     */
    protected function checkPreviousFailedAttempts(): void
    {
        $failedAttempts = RateLimiter::attempts($this->throttleKey());
        
        if ($failedAttempts >= 3) {
            $this->showForgotPasswordSuggestion = true;
        }
    }

    /**
     * Go to forgot password page
     */
    public function goToForgotPassword(): void
    {
        $this->redirect(route('password.request', ['email' => $this->email]), navigate: true);
    }
}
