<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectBasedOnRole($request->user());
        }

        if ($request->user()->markEmailAsVerified()) {
            /** @var \Illuminate\Contracts\Auth\MustVerifyEmail $user */
            $user = $request->user();

            event(new Verified($user));
        }

        return $this->redirectBasedOnRole($request->user());
    }

    /**
     * Kullanıcının rolüne göre uygun panele yönlendir
     */
    private function redirectBasedOnRole($user): RedirectResponse
    {
        $redirectRoute = match ($user->role) {
            'admin' => 'admin.home',
            'personel' => 'personel.home',
            default => 'home'
        };

        return redirect()->intended(route($redirectRoute, absolute: false).'?verified=1');
    }
}
