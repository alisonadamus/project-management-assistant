<?php

namespace Alison\ProjectManagementAssistant\Console\Commands;

use Alison\ProjectManagementAssistant\Events\MessageSent;
use Alison\ProjectManagementAssistant\Models\Message;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Console\Command;

class TestChatCommand extends Command
{
    protected $signature = 'test:chat';
    protected $description = 'Тестування функціональності чату';

    public function handle()
    {
        $this->info('Початок тестування чату...');

        // Знаходимо проект з призначеним студентом
        $project = Project::whereNotNull('assigned_to')->first();
        
        if (!$project) {
            $this->error('Не знайдено проектів з призначеним студентом');
            return 1;
        }

        $this->info("Тестуємо проект: {$project->name} (ID: {$project->id})");
        $this->info("Призначений студент: {$project->assigned_to}");

        // Створюємо тестове повідомлення
        $message = Message::create([
            'project_id' => $project->id,
            'sender_id' => $project->assigned_to,
            'message' => 'Тестове повідомлення для перевірки WebSocket',
            'is_read' => false,
        ]);

        $message->load('sender');

        $messageData = [
            'id' => $message->id,
            'message' => $message->message,
            'message_html' => $message->message_html ?? $message->message,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->full_name,
            'is_read' => $message->is_read,
            'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            'is_mine' => false,
        ];

        $this->info('Відправка повідомлення через WebSocket...');

        try {
            broadcast(new MessageSent($project->id, $messageData));
            $this->info('✅ Повідомлення успішно відправлено через WebSocket');
        } catch (\Exception $e) {
            $this->error("❌ Помилка відправки через WebSocket: {$e->getMessage()}");
        }

        $this->info("Повідомлення створено з ID: {$message->id}");
        
        return 0;
    }
}
