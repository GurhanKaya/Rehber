<?php

require __DIR__.'/auth.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TaskFileController;
use App\Http\Controllers\LanguageController;
use App\Livewire\Guest\UserSearch;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Admin\UserList;
use App\Livewire\Admin\UserCreate;
use App\Livewire\Admin\UserEdit;
use App\Livewire\Admin\ProfileEdit as AdminProfileEdit;
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

// GÜVENLİ DOSYA İNDİRME VE GÖRÜNTÜLEME (kimliği doğrulanmamış istekler 403 alır)
Route::get('/files/{taskFile}/view', [TaskFileController::class, 'view'])->middleware(['auth','can:viewAdminPanel'])->name('files.view');
Route::get('/files/{taskFile}/download', [TaskFileController::class, 'download'])->middleware(['auth','can:downloadFiles'])->name('files.download');

// ADMIN PANEL
Route::middleware(['auth', 'can:viewAdminPanel'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Home::class)->name('home');
        Route::get('/profile', AdminProfileEdit::class)->name('profile.edit');
        Route::get('/users', UserList::class)->name('users');
        Route::get('/users/create', UserCreate::class)->name('users.create');
        Route::get('/users/{user}/edit', UserEdit::class)->name('users.edit');
        Route::get('/tasks', \App\Livewire\Admin\TaskManager::class)->name('tasks');
        Route::get('/tasks/create', \App\Livewire\Admin\TaskCreate::class)->name('tasks.create');
        Route::get('/tasks/{task}', \App\Livewire\Admin\TaskDetail::class)->name('tasks.detail');
        Route::get('/appointments', \App\Livewire\Admin\AppointmentManager::class)->name('appointments');
        Route::get('/appointments/{appointment}/edit', \App\Livewire\Admin\AppointmentEdit::class)->name('appointments.edit');
        Route::get('/appointment-slots', \App\Livewire\Admin\AppointmentSlotManager::class)->name('appointment.slots');
        Route::get('/departments', \App\Livewire\Admin\DepartmentManager::class)->name('departments');
        Route::get('/titles', \App\Livewire\Admin\TitleManager::class)->name('titles');
    });

// PERSONEL PANEL
Route::middleware(['auth', 'verified', 'can:viewPersonelPanel'])
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
