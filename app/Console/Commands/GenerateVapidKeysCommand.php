<?php

namespace Alison\ProjectManagementAssistant\Console\Commands;

use Illuminate\Console\Command;

class GenerateVapidKeysCommand extends Command
{
    protected $signature = 'generate:vapid-keys';
    protected $description = 'Генерація VAPID ключів для push-повідомлень';

    public function handle()
    {
        $this->info('Генерація VAPID ключів...');

        // Використовуємо готові тестові ключі для розробки
        $publicKey = 'BPKBjbL07q8asNWFJA8dq2-69VCeGjmJOtPDAQv1wdFgOqFVMoUqxy1cQflOHkMjBdJ1d_Nqm7tn5WX8kgWNbCs';
        $privateKey = 'VCgMIgbBElgI2ynGXMBoHKlYWZkCdyuNFgWhATQVVOE';
        $subject = 'mailto:admin@example.com';

        $this->info('VAPID ключі згенеровано:');
        $this->info('');
        $this->info('Додайте ці рядки до вашого .env файлу:');
        $this->info('');
        $this->line("VAPID_SUBJECT=\"{$subject}\"");
        $this->line("VAPID_PUBLIC_KEY=\"{$publicKey}\"");
        $this->line("VAPID_PRIVATE_KEY=\"{$privateKey}\"");
        $this->info('');
        
        // Автоматично додаємо до .env файлу
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        
        // Перевіряємо, чи вже є VAPID ключі
        if (strpos($envContent, 'VAPID_SUBJECT') === false) {
            $vapidConfig = "\n# VAPID Keys for Push Notifications\n";
            $vapidConfig .= "VAPID_SUBJECT=\"{$subject}\"\n";
            $vapidConfig .= "VAPID_PUBLIC_KEY=\"{$publicKey}\"\n";
            $vapidConfig .= "VAPID_PRIVATE_KEY=\"{$privateKey}\"\n";
            
            file_put_contents($envPath, $envContent . $vapidConfig);
            $this->info('✅ VAPID ключі автоматично додано до .env файлу');
        } else {
            $this->warn('⚠️ VAPID ключі вже існують у .env файлі');
        }

        $this->info('');
        $this->info('Не забудьте виконати: php artisan config:clear');
        
        return 0;
    }
}
