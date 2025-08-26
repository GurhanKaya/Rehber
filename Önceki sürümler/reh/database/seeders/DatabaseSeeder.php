<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 5 admin kullanıcı
        User::factory()->count(5)->state([
            'role' => 'admin',
            'title' => 'Yönetici',
            'department' => 'Rektörlük',
        ])->create();

        // 25 normal kullanıcı
        User::factory()->count(25)->state([
            'role' => 'personel',
        ])->create();
    }
}
