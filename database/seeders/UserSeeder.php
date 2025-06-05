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

        // Масиви українських імен
        $teacherNames = [
            ['first_name' => 'Олена', 'last_name' => 'Коваленко', 'middle_name' => 'Петрівна'],
            ['first_name' => 'Андрій', 'last_name' => 'Шевченко', 'middle_name' => 'Володимирович'],
            ['first_name' => 'Марія', 'last_name' => 'Іваненко', 'middle_name' => 'Олександрівна'],
            ['first_name' => 'Василь', 'last_name' => 'Мельник', 'middle_name' => 'Іванович'],
            ['first_name' => 'Тетяна', 'last_name' => 'Бондаренко', 'middle_name' => 'Сергіївна'],
        ];

        $studentNames = [
            ['first_name' => 'Дмитро', 'last_name' => 'Петренко', 'middle_name' => 'Олегович'],
            ['first_name' => 'Анна', 'last_name' => 'Савченко', 'middle_name' => 'Миколаївна'],
            ['first_name' => 'Максим', 'last_name' => 'Кравченко', 'middle_name' => 'Андрійович'],
            ['first_name' => 'Софія', 'last_name' => 'Лисенко', 'middle_name' => 'Вікторівна'],
            ['first_name' => 'Олександр', 'last_name' => 'Ткаченко', 'middle_name' => 'Сергійович'],
            ['first_name' => 'Вікторія', 'last_name' => 'Морозенко', 'middle_name' => 'Олександрівна'],
            ['first_name' => 'Ігор', 'last_name' => 'Гриценко', 'middle_name' => 'Васильович'],
            ['first_name' => 'Катерина', 'last_name' => 'Романенко', 'middle_name' => 'Дмитрівна'],
            ['first_name' => 'Артем', 'last_name' => 'Левченко', 'middle_name' => 'Миколайович'],
            ['first_name' => 'Юлія', 'last_name' => 'Павленко', 'middle_name' => 'Ігорівна'],
        ];

        // Створення викладачів
        for ($i = 0; $i < count($teacherNames); $i++) {
            $teacher = User::factory()->create([
                'email' => 'teacher' . ($i + 1) . '@uzhnu.edu.ua',
                'first_name' => $teacherNames[$i]['first_name'],
                'last_name' => $teacherNames[$i]['last_name'],
                'middle_name' => $teacherNames[$i]['middle_name'],
                'course_number' => null,
                'password' => Hash::make('12345678'),
            ]);
            $teacher->assignRole('teacher');
        }

        // Створення студентів
        for ($i = 0; $i < count($studentNames); $i++) {
            $student = User::factory()->create([
                'email' => 'student' . ($i + 1) . '@student.uzhnu.edu.ua',
                'first_name' => $studentNames[$i]['first_name'],
                'last_name' => $studentNames[$i]['last_name'],
                'middle_name' => $studentNames[$i]['middle_name'],
                'course_number' => rand(2, 4),
                'password' => Hash::make('12345678'),
            ]);
            $student->assignRole('student');
        }
    }
}
