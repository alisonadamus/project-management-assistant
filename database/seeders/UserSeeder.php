<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Створення адміністратора
        $admin = User::query()->create([
            'name' => 'alisaadamus',
            'email' => 'alisaadamus.aa@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'first_name' => 'Alisa',
            'last_name' => 'Adamus',
            'middle_name' => null,
            'description' => null,
            'avatar' => null,
            'course_number' => null,
        ]);

        // Призначення ролі адміністратора
        $admin->assignRole('admin');

        // Створення викладачів
        for ($i = 1; $i <= 5; $i++) {
            $teacher = User::factory()->create([
                'email' => 'teacher' . $i . '@uzhnu.edu.ua',
                'course_number' => null,
                'password' => Hash::make('12345678'),
            ]);
            $teacher->assignRole('teacher');
        }

        // Створення студентів
        for ($i = 1; $i <= 10; $i++) {
            $student = User::factory()->create([
                'email' => 'student' . $i . '@student.uzhnu.edu.ua',
                'course_number' => rand(1, 4),
                'password' => Hash::make('12345678'),
            ]);
            $student->assignRole('student');
        }
    }
}
