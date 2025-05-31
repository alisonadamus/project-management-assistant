<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Category::query()->count() === 0) {
            $this->call(CategorySeeder::class);
        }

/*        $categories = Category::all();
        Event::factory()->count(10)->create([
            'category_id' => fn() => $categories->random()->id,
        ]);*/
        $categories = Category::all();

        $events = [
            [
                'name' => 'Навчальна практика 2025',
                'description' => 'Практика для студентів 2-го курсу з розробки вебдодатків на С#.',
            ],
            [
                'name' => 'Курсова робота 2025',
                'description' => 'Розробка курсових проєктів із баз даних і вебтехнологій.',
            ],
            [
                'name' => 'Переддипломна практика 2025',
                'description' => 'Підготовка до дипломного проєкту з акцентом на командну роботу.',
            ],
            [
                'name' => 'Навчальна практика 2025',
                'description' => 'Практика для студентів 3-го курсу з розробки вебдодатків на Java.',
            ],
            [
                'name' => 'Курсовий проект 2025',
                'description' => 'Розробка курсових проєктів із баз даних і вебтехнологій.',
            ],
            [
                'name' => 'Переддипломна практика 2024',
                'description' => 'Підготовка до дипломного проєкту з акцентом на командну роботу.',
            ],
            [
                'name' => 'Виробнича практика 2025',
                'description' => 'Практика для студентів 4-го курсу з розробки вебдодатків на Java.',
            ],
            [
                'name' => 'Курсовий проект 2024',
                'description' => 'Розробка курсових проєктів із баз даних і вебтехнологій.',
            ],
            [
                'name' => 'Курсова робота 2024',
                'description' => 'Розробка курсових проєктів із баз даних і вебтехнологій.',
            ],
            [
                'name' => 'Навчальна практика 2024',
                'description' => 'Практика для студентів 2-го курсу з розробки вебдодатків на С#.',
            ],
        ];

        foreach ($events as $eventData) {
            Event::factory()->create([
                'category_id' => $categories->random()->id,
                'name' => $eventData['name'],
                'description' => $eventData['description'],
            ]);
        }
    }
}
