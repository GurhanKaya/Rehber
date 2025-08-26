<?php

namespace App\Policies;

use App\Models\User;

class PanelPolicy
{
    /**
     * Admin rolü kontrolü
     */
    public function admin(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Personel rolü kontrolü
     */
    public function personel(User $user): bool
    {
        return $user->role === 'personel';
    }

    /**
     * Admin paneline erişim kontrolü
     */
    public function viewAdminPanel(User $user): bool
    {
        return $this->admin($user);
    }

    /**
     * Personel paneline erişim kontrolü
     */
    public function viewPersonelPanel(User $user): bool
    {
        return $this->personel($user);
    }

    /**
     * Kullanıcı yönetimi yetkisi
     */
    public function manageUsers(User $user): bool
    {
        return $this->admin($user);
    }

    /**
     * Görev yönetimi yetkisi
     */
    public function manageTasks(User $user): bool
    {
        return in_array($user->role, ['admin', 'personel']);
    }

    /**
     * Randevu yönetimi yetkisi
     */
    public function manageAppointments(User $user): bool
    {
        return in_array($user->role, ['admin', 'personel']);
    }

    /**
     * Sistem ayarları yetkisi
     */
    public function manageSettings(User $user): bool
    {
        return $this->admin($user);
    }

    /**
     * Profil düzenleme yetkisi (kendi profilini veya admin ise herkesi)
     */
    public function editProfile(User $user, User $targetUser = null): bool
    {
        if ($this->admin($user)) {
            return true;
        }
        
        // Kendi profilini düzenleyebilir
        return $targetUser ? $user->id === $targetUser->id : true;
    }

    /**
     * Dosya indirme yetkisi
     */
    public function downloadFiles(User $user): bool
    {
        return in_array($user->role, ['admin', 'personel']);
    }

    /**
     * Dosya indirme yetkisi (TaskFile model için)
     */
    public function download(User $user, $taskFile = null): bool
    {
        return $this->downloadFiles($user);
    }
}
