<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Önce Department ve Title'ları oluştur
        $this->call(DepartmentTitleSeeder::class);

        // Rektörlük departmanını ve yönetici title'ını bul
        $rektorluk = \App\Models\Department::where('name', 'Rektörlük')->first();
        $rektorTitle = \App\Models\Title::where('name', 'Rektör')->where('department_id', $rektorluk->id)->first();

        // 5 admin kullanıcı
        User::factory()->count(5)->state([
            'role' => 'admin',
            'department_id' => $rektorluk->id,
            'title_id' => $rektorTitle->id,
        ])->create();

        // 25 normal kullanıcı
        User::factory()->count(25)->state([
            'role' => 'personel',
        ])->create();
    }
}
