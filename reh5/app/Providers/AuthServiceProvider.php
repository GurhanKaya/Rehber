<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\TaskFile;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Policy mapping'ler burada tanımlanır
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate tanımları
        Gate::define('viewAdminPanel', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('viewPersonelPanel', function (User $user) {
            return $user->role === 'personel';
        });

        Gate::define('downloadFiles', function (User $user) {
            return in_array($user->role, ['admin', 'personel']);
        });

        Gate::define('manageUsers', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('manageTasks', function (User $user) {
            return in_array($user->role, ['admin', 'personel']);
        });

        Gate::define('manageAppointments', function (User $user) {
            return in_array($user->role, ['admin', 'personel']);
        });
    }
}