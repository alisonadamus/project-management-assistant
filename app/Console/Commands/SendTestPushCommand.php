<?php

namespace Alison\ProjectManagementAssistant\Console\Commands;

use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Console\Command;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class SendTestPushCommand extends Command
{
    protected $signature = 'send:test-push {userId?}';
    protected $description = 'Відправка тестового push-повідомлення';

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

        $this->info("Відправка тестового push-повідомлення для: {$user->full_name}");

        // Перевіряємо підписки
        $subscriptions = $user->pushSubscriptions;
        $this->info("Кількість підписок: " . $subscriptions->count());

        if ($subscriptions->count() === 0) {
            $this->warn('У користувача немає push підписок');
            $this->info('Інструкції:');
            $this->info('1. Відкрийте http://127.0.0.1:8000/test-push.html');
            $this->info('2. Натисніть "Перевірити підтримку"');
            $this->info('3. Натисніть "Запросити дозвіл"');
            $this->info('4. Натисніть "Підписатися"');
            $this->info('5. Увійдіть в систему і увімкніть push в профілі');
            return 0;
        }

        foreach ($subscriptions as $subscription) {
            $this->info("Підписка: " . substr($subscription->endpoint, 0, 50) . '...');
        }

        try {
            // Створюємо повідомлення
            $message = WebPushMessage::create()
                ->title('Тестове повідомлення')
                ->body('Це тестове push-повідомлення з Laravel!')
                ->icon('/favicon.ico')
                ->badge('/favicon.ico')
                ->data(['url' => '/dashboard'])
                ->options(['TTL' => 1000]);

            // Відправляємо через канал
            $channel = new WebPushChannel();
            $channel->send($user, $message);

            $this->info('✅ Тестове push-повідомлення відправлено!');

        } catch (\Exception $e) {
            $this->error("❌ Помилка відправки: {$e->getMessage()}");
            $this->error("Стек: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
