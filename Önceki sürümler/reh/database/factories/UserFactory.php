<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName,
            'surname' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // varsayılan şifre
            'role' => $this->faker->randomElement(['admin', 'personel']),
            'title' => $this->faker->randomElement(['Uzman', 'Teknisyen', 'Personel', 'Sekreter']),
            'phone' => $this->faker->optional()->phoneNumber,
            'department' => $this->faker->randomElement([
                'Bilgi İşlem',
                'Öğrenci İşleri',
                'Personel Daire Başkanlığı',
                'İdari Mali İşler',
                'Yazı İşleri',
                'Rektörlük'
            ]),
            'photo' => null,
        ];
    }
}
