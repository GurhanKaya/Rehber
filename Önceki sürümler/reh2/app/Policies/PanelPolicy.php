<?php
namespace App\Policies;

use App\Models\User;

class PanelPolicy
{
    public function admin(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function personel(User $user): bool
    {
        return $user->role === 'personel';
    }

}
