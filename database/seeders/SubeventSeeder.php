<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Subevent;
use Database\Factories\SubeventFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SubeventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Event::query()->count() === 0) {
            $this->call(EventSeeder::class);
        }

        $subevents = [
            [
                'name' => 'Вибір теми',
                'description' => 'Етап вибору теми для проєкту або практики.',
            ],
            [
                'name' => 'Аналіз вимог',
                'description' => 'Аналіз вимог до курсового проєкту.',
            ],
            [
                'name' => 'Розробка прототипу',
                'description' => 'Розробка та тестування прототипу проєкту.',
            ],
            [
                'name' => 'Розробка фінальної версії',
                'description' => 'Розробка та тестування фінальної версії проєкту.',
            ],
            [
                'name' => 'Теоретична частина',
                'description' => 'Написання теоретичної частини звітної роботи.',
            ],
            [
                'name' => 'Практична частина',
                'description' => 'Написання практично частини звітної роботи.',
            ],
            [
                'name' => 'Захист проекту',
                'description' => 'Презентація та захист проєкту перед комісією.',
            ],
        ];


        $events = Event::all();
        foreach ($events as $event) {
            $eventDurationDays = $event->start_date->diffInDays($event->end_date ?: Carbon::now()->addDays(30));

            foreach ($subevents as $index => $subeventData) {
                $startOffset = rand(0, max(0, $eventDurationDays - 7));
                $endOffset = $startOffset + rand(1, min(7, $eventDurationDays - $startOffset));

                $startDate = $event->start_date->copy()->addDays($startOffset);
                $endDate = $event->end_date ? $event->start_date->copy()->addDays($endOffset) : null;

                Subevent::factory()->create([
                    'event_id' => $event->id,
                    'name' => $subeventData['name'],
                    'description' => $subeventData['description'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
            }
        }
    }
}
