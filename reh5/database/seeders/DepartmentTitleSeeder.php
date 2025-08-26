<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Title;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DepartmentTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Varsayılan departmanları oluştur
        $departments = [
            [
                'name' => 'Bilgi İşlem',
                'description' => 'Bilgi teknolojileri ve sistem yönetimi',
                'titles' => ['Yazılım Geliştirici', 'Sistem Yöneticisi', 'Teknisyen', 'Uzman']
            ],
            [
                'name' => 'Öğrenci İşleri',
                'description' => 'Öğrenci kayıt ve akademik işlemler',
                'titles' => ['Uzman', 'Memur', 'Sekreter', 'Şef']
            ],
            [
                'name' => 'Personel Daire Başkanlığı',
                'description' => 'İnsan kaynakları ve personel işlemleri',
                'titles' => ['Daire Başkanı', 'Şef', 'Uzman', 'Memur']
            ],
            [
                'name' => 'İdari Mali İşler',
                'description' => 'Mali işlemler ve muhasebe',
                'titles' => ['Şef', 'Uzman', 'Muhasebeci', 'Memur']
            ],
            [
                'name' => 'Yazı İşleri',
                'description' => 'Evrak ve yazışma işlemleri',
                'titles' => ['Sekreter', 'Memur', 'Uzman']
            ],
            [
                'name' => 'Rektörlük',
                'description' => 'Üst yönetim ve karar alma',
                'titles' => ['Rektör', 'Rektör Yardımcısı', 'Özel Kalem Müdürü', 'Sekreter']
            ],
        ];

        foreach ($departments as $deptData) {
            $department = Department::create([
                'name' => $deptData['name'],
                'description' => $deptData['description'],
                'is_active' => true,
            ]);

            // Bu departman için title'ları oluştur
            foreach ($deptData['titles'] as $titleName) {
                Title::create([
                    'name' => $titleName,
                    'department_id' => $department->id,
                    'description' => null,
                    'is_active' => true,
                ]);
            }
        }

        // Mevcut user'ların department ve title değerlerini migrate et
        $this->migrateExistingUsers();
    }

    private function migrateExistingUsers()
    {
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            $departmentId = null;
            $titleId = null;

            // Department mapping
            if ($user->department) {
                $department = Department::where('name', $user->department)->first();
                if ($department) {
                    $departmentId = $department->id;
                }
            }

            // Title mapping
            if ($user->title && $departmentId) {
                $title = Title::where('name', $user->title)
                           ->where('department_id', $departmentId)
                           ->first();
                if ($title) {
                    $titleId = $title->id;
                } else {
                    // Eğer title bulunamazsa, bu departmanda "Diğer" title'ı oluştur
                    $title = Title::firstOrCreate([
                        'name' => $user->title,
                        'department_id' => $departmentId,
                    ], [
                        'description' => 'Otomatik oluşturulan title',
                        'is_active' => true,
                    ]);
                    $titleId = $title->id;
                }
            }

            // User'ı güncelle
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'department_id' => $departmentId,
                    'title_id' => $titleId,
                ]);
        }
    }
}
