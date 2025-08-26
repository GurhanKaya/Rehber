<?php

require __DIR__.'/auth.php';

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskFileController;
use App\Http\Controllers\LanguageController;
use App\Livewire\Guest\UserSearch;
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

// DİL DEĞİŞTİRME
Route::get('/language/{locale}', [LanguageController::class, 'changeLanguage'])->name('language.change');

// PUBLIC ANA SAYFA
Route::get('/', UserSearch::class)->name('home');
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::get('/randevu/{id}', \App\Livewire\Guest\BookAppointment::class)->name('guest.book-appointment');

// DASHBOARD REDIRECT
Route::get('/dashboard', function () {
    return redirect()->route('personel.home');
})->name('dashboard');

// GÜVENLI DOSYA İNDİRME
Route::middleware(['auth'])->group(function () {
    Route::get('/files/{taskFile}/download', [TaskFileController::class, 'download'])->name('files.download');
});

// ADMIN PANEL
Route::middleware(['auth', 'can:admin,App\Policies\PanelPolicy'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Home::class)->name('home');
        Route::get('/users', UserList::class)->name('users');
        Route::get('/users/create', UserCreate::class)->name('users.create');
        Route::get('/users/{user}/edit', UserEdit::class)->name('users.edit');
        Route::get('/tasks', \App\Livewire\Admin\TaskManager::class)->name('tasks');
        Route::get('/tasks/create', \App\Livewire\Admin\TaskCreate::class)->name('tasks.create');
        Route::get('/tasks/{task}', \App\Livewire\Admin\TaskDetail::class)->name('tasks.detail');
        Route::get('/appointments', \App\Livewire\Admin\AppointmentManager::class)->name('appointments');
        Route::get('/appointments/{appointment}/edit', \App\Livewire\Admin\AppointmentEdit::class)->name('appointments.edit');
        Route::get('/appointment-slots', \App\Livewire\Admin\AppointmentSlotManager::class)->name('appointment.slots');
    });

// PERSONEL PANEL
Route::middleware(['auth', 'verified', 'can:personel,App\Policies\PanelPolicy'])
    ->prefix('personel')->name('personel.')->group(function () {
        Route::get('/', Home::class)->name('home');
        Route::get('/users', PersonelUserList::class)->name('users');
        Route::get('/profile/edit', \App\Livewire\Personel\ProfileEdit::class)->name('profile.edit');
        Route::get('/randevu-saatlerim', \App\Livewire\Personel\AppointmentSlots::class)->name('randevu-saatlerim');
        Route::get('/randevularim', \App\Livewire\Personel\Appointments::class)->name('randevularim');
        Route::get('/gorevler', \App\Livewire\Personel\TaskList::class)->name('tasks');
        Route::get('/gorevler/{task}', \App\Livewire\Personel\TaskDetail::class)->name('tasks.detail');
        Route::get('/public-gorevler', \App\Livewire\Personel\PublicTaskList::class)->name('public-tasks');
    });
