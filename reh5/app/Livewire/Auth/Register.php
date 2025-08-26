<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';
    public string $surname = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Varsayılan olarak personel rolü ata
        $validated['role'] = 'personel';

        $user = User::create($validated);

        event(new Registered($user));

        Auth::login($user);

        $redirectRoute = $user->role === 'admin' ? 'admin.users' : 'personel.home';

        $this->redirect(route($redirectRoute), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register')->with('title', 'Kayıt Ol');
    }
}
