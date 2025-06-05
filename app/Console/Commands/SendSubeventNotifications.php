<?php

namespace Alison\ProjectManagementAssistant\Console\Commands;

use Alison\ProjectManagementAssistant\Models\Subevent;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Notifications\SubeventStartNotification;
use Alison\ProjectManagementAssistant\Notifications\SubeventStartingSoonNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendSubeventNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-subevent-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Надсилає повідомлення про початок підподій та підподії, які починаються завтра';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Початок надсилання повідомлень про підподії...');

        // Надсилання повідомлень про підподії, які починаються сьогодні
        $this->sendSubeventStartNotifications();

        // Надсилання повідомлень про підподії, які починаються завтра
        $this->sendSubeventStartingSoonNotifications();

        $this->info('Завершено надсилання повідомлень про підподії.');
    }

    /**
     * Надсилання повідомлень про підподії, які починаються сьогодні
     */
    private function sendSubeventStartNotifications()
    {
        $today = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        // Знаходимо підподії, які починаються сьогодні
        $subevents = Subevent::with(['event.category', 'event.supervisors.user'])
            ->whereBetween('start_date', [$today, $endOfDay])
            ->get();

        $this->info("Знайдено {$subevents->count()} підподій, які починаються сьогодні");

        foreach ($subevents as $subevent) {
            $this->sendNotificationsForSubevent($subevent, SubeventStartNotification::class, 'початок');
        }
    }

    /**
     * Надсилання повідомлень про підподії, які починаються завтра
     */
    private function sendSubeventStartingSoonNotifications()
    {
        $tomorrow = now()->addDay()->startOfDay();
        $endOfTomorrow = now()->addDay()->endOfDay();

        // Знаходимо підподії, які починаються завтра
        $subevents = Subevent::with(['event.category', 'event.supervisors.user'])
            ->whereBetween('start_date', [$tomorrow, $endOfTomorrow])
            ->get();

        $this->info("Знайдено {$subevents->count()} підподій, які починаються завтра");

        foreach ($subevents as $subevent) {
            $this->sendNotificationsForSubevent($subevent, SubeventStartingSoonNotification::class, 'нагадування');
        }
    }

    /**
     * Надсилання повідомлень для конкретної підподії
     */
    private function sendNotificationsForSubevent(Subevent $subevent, string $notificationClass, string $type)
    {
        try {
            // Отримуємо користувачів, які мають доступ до події (і відповідно до підподії)
            $users = $this->getUsersForSubevent($subevent);

            $this->info("Надсилання повідомлень про {$type} підподії '{$subevent->name}' для {$users->count()} користувачів");

            foreach ($users as $user) {
                try {
                    $user->notify(new $notificationClass($subevent));
                    $this->line("✓ Надіслано повідомлення користувачу: {$user->email}");
                } catch (\Exception $e) {
                    $this->error("✗ Помилка надсилання повідомлення користувачу {$user->email}: {$e->getMessage()}");
                    Log::error("Помилка надсилання повідомлення про підподію", [
                        'subevent_id' => $subevent->id,
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            $this->error("✗ Помилка обробки підподії '{$subevent->name}': {$e->getMessage()}");
            Log::error("Помилка обробки підподії", [
                'subevent_id' => $subevent->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Отримання користувачів, які мають доступ до підподії (через основну подію)
     */
    private function getUsersForSubevent(Subevent $subevent)
    {
        $users = collect();
        $event = $subevent->event;

        // Студенти з відповідним курсом
        $students = User::role('student')
            ->where('course_number', $event->category->course_number)
            ->get();

        $users = $users->merge($students);

        // Викладачі, які є науковими керівниками в цій події
        $supervisorUserIds = $event->supervisors->pluck('user_id');
        $teachers = User::role('teacher')
            ->whereIn('id', $supervisorUserIds)
            ->get();

        $users = $users->merge($teachers);

        // Видаляємо дублікати за email
        return $users->unique('email');
    }
}
