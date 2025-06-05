<?php

namespace Alison\ProjectManagementAssistant\Console\Commands;

use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Notifications\NewChatMessageNotification;
use Alison\ProjectManagementAssistant\Models\Message;
use Alison\ProjectManagementAssistant\Models\Project;
use Illuminate\Console\Command;

class TestPushNotificationCommand extends Command
{
    protected $signature = 'test:push-notification {userId?}';
    protected $description = 'Тестування push-повідомлень';

    public function handle()
    {
        $userId = $this->argument('userId');
        
        if ($userId) {
            $user = User::find($userId);
        } else {
            $user = User::first();
        }
        
        if (!$user) {
            $this->error('Користувач не знайдений');
            return 1;
        }

        $this->info("Тестування push-повідомлень для користувача: {$user->full_name}");

        // Перевіряємо, чи є у користувача push підписки
        $subscriptions = $user->pushSubscriptions;
        $this->info("Кількість push підписок: " . $subscriptions->count());

        if ($subscriptions->count() === 0) {
            $this->warn('У користувача немає активних push підписок');
            $this->info('Для тестування push-повідомлень:');
            $this->info('1. Відкрийте браузер і увійдіть в систему');
            $this->info('2. Перейдіть в Профіль -> Push сповіщення');
            $this->info('3. Увімкніть push сповіщення');
            $this->info('4. Запустіть цю команду знову');
            return 0;
        }

        // Створюємо тестове повідомлення
        $project = Project::whereNotNull('assigned_to')->first();
        if (!$project) {
            $this->error('Не знайдено проектів з призначеним студентом');
            return 1;
        }

        $message = Message::create([
            'project_id' => $project->id,
            'sender_id' => $project->assigned_to,
            'message' => 'Тестове push-повідомлення з чату',
            'is_read' => false,
        ]);

        $message->load('sender');

        try {
            // Відправляємо push-повідомлення
            $user->notify(new NewChatMessageNotification($message));
            $this->info('✅ Push-повідомлення відправлено успішно!');
            $this->info("Повідомлення ID: {$message->id}");
            $this->info("Проект: {$project->name}");
        } catch (\Exception $e) {
            $this->error("❌ Помилка відправки push-повідомлення: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
