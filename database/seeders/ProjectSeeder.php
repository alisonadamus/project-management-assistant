<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\Technology;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Event::query()->count() === 0) {
            $this->call(EventSeeder::class);
        }

        if (Supervisor::query()->count() === 0) {
            $this->call(SupervisorSeeder::class);
        }

        if (User::query()->count() === 0) {
            $this->call(UserSeeder::class);
        }

        if (Technology::query()->count() === 0) {
            $this->call(TechnologySeeder::class);
        }

        $events = Event::all();
        $supervisors = Supervisor::all();
        $users = User::role('student')->get();

        $projects_data = [
            [
                'name' => 'Розробка вебдодатку на Laravel',
                'body' => 'Створення системи управління проєктами з автентифікацією та чатом.',
            ],
            [
                'name' => 'Аналіз даних із Python',
                'body' => 'Обробка великих наборів даних за допомогою Pandas і NumPy.',
            ],
            [
                'name' => 'Мобільний додаток для освіти',
                'body' => 'Розробка додатку для Android із функціями планування.',
            ],
            [
                'name' => 'Дослідження кібербезпеки',
                'body' => 'Аналіз вразливостей вебдодатків і методів захисту.',
            ],
            [
                'name' => 'Автоматизація CI/CD',
                'body' => 'Налаштування конвеєра розгортання з GitHub Actions.',
            ],
            [
                'name' => 'UI/UX дизайн для платформи',
                'body' => 'Створення прототипів інтерфейсу в Figma.',
            ],
            [
                'name' => 'Розробка API для системи',
                'body' => 'Створення RESTful API для управління подіями.',
            ],
            [
                'name' => 'Машинне навчання для прогнозування',
                'body' => 'Побудова моделі прогнозування з TensorFlow.',
            ],
            [
                'name' => 'База даних для e-commerce',
                'body' => 'Проектування реляційної бази даних із PostgreSQL.',
            ],
            [
                'name' => 'Хакатон-прототип IoT',
                'body' => 'Розробка системи для збору даних із датчиків.',
            ],
            [
                'name' => 'Система управління заявками',
                'body' => 'Автоматизація обробки студентських заявок.',
            ],
            [
                'name' => 'Вебсайт для університету',
                'body' => 'Створення адаптивного сайту на Tailwind CSS.',
            ],
            [
                'name' => 'Чат-бот для підтримки',
                'body' => 'Розробка бота для автоматизації відповідей.',
            ],
            [
                'name' => 'Аналіз соціальних мереж',
                'body' => 'Дослідження даних із Twitter API.',
            ],
            [
                'name' => 'Розробка гри на Unity',
                'body' => 'Створення 2D-гри для навчальних цілей.',
            ],
            [
                'name' => 'Оптимізація бази даних',
                'body' => 'Покращення продуктивності SQL-запитів.',
            ],
            [
                'name' => 'Система рекомендацій',
                'body' => 'Розробка алгоритму рекомендацій для проєктів.',
            ],
            [
                'name' => 'Тестування вебдодатків',
                'body' => 'Автоматизація тестування з Selenium.',
            ],
            [
                'name' => 'Розробка CMS',
                'body' => 'Створення системи управління контентом.',
            ],
            [
                'name' => 'Дослідження блокчейн',
                'body' => 'Аналіз смарт-контрактів на Ethereum.',
            ],
        ];

/*        $projects = Project::factory()->count(40)->create(
            [
                'event_id' => fn() => $events->random()->id,
                'supervisor_id' => fn() => $supervisors->random()->id,
                'assigned_to' => fn() => rand(0, 1) ? $users->random()->id : null,
            ]
        );*/
        foreach ($projects_data as $projectData) {
            Project::factory()->create([
                'event_id' => fn() => $events->random()->id,
                'supervisor_id' => fn() => $supervisors->random()->id,
                'assigned_to' => fn() => rand(0, 1) ? $users->random()->id : null,
                'name' => $projectData['name'],
                'body' => $projectData['body'],
            ]);
        }

        $projects = Project::all();
        $technologies = Technology::all();
        foreach ($projects as $project) {
            $randomTechnologies = $technologies->random(rand(2, 5));
            $project->technologies()->attach($randomTechnologies);
        }
    }
}
