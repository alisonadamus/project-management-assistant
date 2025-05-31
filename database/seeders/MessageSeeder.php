<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\Message;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Project::query()->count() === 0) {
            $this->call(ProjectSeeder::class);
        }

        $projects = Project::all()->whereNotNull('assigned_to');
        $supervisors = Supervisor::all();

        $messages = [
            [
                'message' => 'Добрий день! Чи можете уточнити вимоги до курсової роботи з баз даних?',
            ],
            [
                'message' => 'Надіслав чернетку проєкту. Прошу перевірити розділ з архітектурою.',
            ],
            [
                'message' => 'Рекомендую додати розділ про тестування до вашого звіту.',
            ],
            [
                'message' => 'Чи можна перенести дедлайн для подачі прототипу на наступний тиждень?',
            ],
            [
                'message' => 'Ваш прототип виглядає добре, але додайте документацію API.',
            ],
            [
                'message' => 'Виникли проблеми з налаштуванням Laravel. Чи можете порадити?',
            ],
            [
                'message' => 'Для налаштування Laravel перевірте офіційну документацію та встановіть Composer.',
            ],
            [
                'message' => 'Я завершив UI/UX дизайн. Чи потрібні зміни перед презентацією?',
            ],
            [
                'message' => 'Презентація запланована на п’ятницю. Будь ласка, підготуйте слайди.',
            ],
            [
                'message' => 'Дякую за відгук! Вніс правки до коду, готовий до фінальної перевірки.',
            ],
        ];

/*        Message::factory()->count(30)->create([
            'project_id' => fn() => $projects->random()->id,
            'sender_id' => fn($attributes) => collect([
                $supervisors->firstWhere('id', $projects->firstWhere('id', $attributes['project_id'])->supervisor_id)->user_id,
                $projects->firstWhere('id', $attributes['project_id'])->assigned_to,
            ])->random(),
        ]);*/
        foreach ($messages as $messageData) {
            Message::factory()->create([
                'project_id' => fn() => $projects->random()->id,
                'sender_id' => fn($attributes) => collect([
                    $supervisors->firstWhere('id', $projects->firstWhere('id', $attributes['project_id'])->supervisor_id)->user_id,
                    $projects->firstWhere('id', $attributes['project_id'])->assigned_to,
                ])->random(),
                'message' => $messageData['message'],
            ]);
        }
    }
}
