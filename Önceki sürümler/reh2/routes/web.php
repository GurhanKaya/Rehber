<?php

require __DIR__.'/auth.php';

use Illuminate\Support\Facades\Route;
use App\Livewire\UserSearch;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Admin\UserList;
use App\Livewire\Admin\UserCreate;
use App\Livewire\Admin\UserEdit;
use App\Livewire\Personel\Home;
use App\Livewire\Personel\UserList as PersonelUserList;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;

// PUBLIC ANA SAYFA
Route::get('/', UserSearch::class)->name('home');
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

// ADMIN PANEL
Route::middleware(['auth', 'can:admin,App\Policies\PanelPolicy'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Home::class)->name('home');
        Route::get('/users', UserList::class)->name('users');
        Route::get('/users/create', UserCreate::class)->name('users.create');
        Route::get('/users/{user}/edit', UserEdit::class)->name('users.edit');
    });

// PERSONEL PANEL
Route::middleware(['auth', 'can:personel,App\Policies\PanelPolicy'])
    ->prefix('personel')->name('personel.')->group(function () {
        Route::get('/', Home::class)->name('home');
        Route::get('/users', PersonelUserList::class)->name('users');
        Route::get('/profile/edit', \App\Livewire\Personel\ProfileEdit::class)->name('profile.edit');
    });
