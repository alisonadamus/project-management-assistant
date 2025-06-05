<?php

namespace Alison\ProjectManagementAssistant\Console\Commands;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Notifications\EventStartNotification;
use Alison\ProjectManagementAssistant\Notifications\EventStartingSoonNotification;
use Alison\ProjectManagementAssistant\Notifications\EventEndingSoonNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendEventStartNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-start-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Надсилає повідомлення про початок подій, події які починаються через 2 дні та події які закінчуються через 2 дні';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Початок надсилання повідомлень про події...');

        // Надсилання повідомлень про події, які починаються сьогодні
        $this->sendEventStartNotifications();

        // Надсилання повідомлень про події, які починаються через 2 дні
        $this->sendEventStartingSoonNotifications();

        // Надсилання повідомлень про події, які закінчуються через 2 дні
        $this->sendEventEndingSoonNotifications();

        $this->info('Завершено надсилання повідомлень про події.');
    }

    /**
     * Надсилання повідомлень про події, які починаються сьогодні
     */
    private function sendEventStartNotifications()
    {
        $today = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        // Знаходимо події, які починаються сьогодні
        $events = Event::with(['category', 'supervisors.user'])
            ->whereBetween('start_date', [$today, $endOfDay])
            ->get();

        $this->info("Знайдено {$events->count()} подій, які починаються сьогодні");

        foreach ($events as $event) {
            $this->sendNotificationsForEvent($event, EventStartNotification::class, 'початок');
        }
    }

    /**
     * Надсилання повідомлень про події, які починаються через 2 дні
     */
    private function sendEventStartingSoonNotifications()
    {
        $twoDaysFromNow = now()->addDays(2)->startOfDay();
        $endOfTwoDaysFromNow = now()->addDays(2)->endOfDay();

        // Знаходимо події, які починаються через 2 дні
        $events = Event::with(['category', 'supervisors.user'])
            ->whereBetween('start_date', [$twoDaysFromNow, $endOfTwoDaysFromNow])
            ->get();

        $this->info("Знайдено {$events->count()} подій, які починаються через 2 дні");

        foreach ($events as $event) {
            $this->sendNotificationsForEvent($event, EventStartingSoonNotification::class, 'нагадування');
        }
    }

    /**
     * Надсилання повідомлень про події, які закінчуються через 2 дні
     */
    private function sendEventEndingSoonNotifications()
    {
        $twoDaysFromNow = now()->addDays(2)->startOfDay();
        $endOfTwoDaysFromNow = now()->addDays(2)->endOfDay();

        // Знаходимо події, які закінчуються через 2 дні
        $events = Event::with(['category', 'supervisors.user'])
            ->whereBetween('end_date', [$twoDaysFromNow, $endOfTwoDaysFromNow])
            ->get();

        $this->info("Знайдено {$events->count()} подій, які закінчуються через 2 дні");

        foreach ($events as $event) {
            $this->sendNotificationsForEvent($event, EventEndingSoonNotification::class, 'нагадування про закінчення');
        }
    }

    /**
     * Надсилання повідомлень для конкретної події
     */
    private function sendNotificationsForEvent(Event $event, string $notificationClass, string $type)
    {
        try {
            // Отримуємо користувачів, які мають доступ до події
            $users = $this->getUsersForEvent($event);

            $this->info("Надсилання повідомлень про {$type} події '{$event->name}' для {$users->count()} користувачів");

            foreach ($users as $user) {
                try {
                    $user->notify(new $notificationClass($event));
                    $this->line("✓ Надіслано повідомлення користувачу: {$user->email}");
                } catch (\Exception $e) {
                    $this->error("✗ Помилка надсилання повідомлення користувачу {$user->email}: {$e->getMessage()}");
                    Log::error("Помилка надсилання повідомлення про подію", [
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            $this->error("✗ Помилка обробки події '{$event->name}': {$e->getMessage()}");
            Log::error("Помилка обробки події", [
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Отримання користувачів, які мають доступ до події
     */
    private function getUsersForEvent(Event $event)
    {
        $users = collect();

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
