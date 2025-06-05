<?php

namespace Alison\ProjectManagementAssistant\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestReverbConnectionCommand extends Command
{
    protected $signature = 'test:reverb-connection';
    protected $description = 'Тестування підключення до Reverb сервера';

    public function handle()
    {
        $this->info('Тестування підключення до Reverb сервера...');

        $host = config('broadcasting.connections.reverb.options.host');
        $port = config('broadcasting.connections.reverb.options.port');
        $scheme = config('broadcasting.connections.reverb.options.scheme');
        
        $this->info("Host: {$host}");
        $this->info("Port: {$port}");
        $this->info("Scheme: {$scheme}");
        
        $baseUrl = "{$scheme}://{$host}:{$port}";

        // Тестуємо основний endpoint
        try {
            $response = Http::timeout(5)->get($baseUrl);
            $this->info("Base URL ({$baseUrl}): {$response->status()}");
        } catch (\Exception $e) {
            $this->error("Base URL error: {$e->getMessage()}");
        }

        // Тестуємо WebSocket endpoint
        try {
            $wsUrl = "{$baseUrl}/app/" . config('broadcasting.connections.reverb.key');
            $response = Http::timeout(5)->get($wsUrl);
            $this->info("WebSocket URL ({$wsUrl}): {$response->status()}");
        } catch (\Exception $e) {
            $this->error("WebSocket URL error: {$e->getMessage()}");
        }

        // Тестуємо broadcasting endpoint
        try {
            $broadcastUrl = "{$baseUrl}/apps/" . config('broadcasting.connections.reverb.app_id') . "/events";
            $response = Http::timeout(5)->post($broadcastUrl, [
                'name' => 'test-event',
                'channel' => 'test-channel',
                'data' => json_encode(['message' => 'test'])
            ]);
            $this->info("Broadcast URL ({$broadcastUrl}): {$response->status()}");
        } catch (\Exception $e) {
            $this->error("Broadcast URL error: {$e->getMessage()}");
        }

        // Тестуємо broadcasting конфігурацію
        $this->info("\n=== Конфігурація Broadcasting ===");
        $this->info("Default connection: " . config('broadcasting.default'));
        $this->info("Reverb APP_ID: " . config('broadcasting.connections.reverb.app_id'));
        $this->info("Reverb APP_KEY: " . config('broadcasting.connections.reverb.key'));
        
        return 0;
    }
}
