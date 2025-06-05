<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Events\MessageSent;
use Alison\ProjectManagementAssistant\Events\MessagesRead;
use Alison\ProjectManagementAssistant\Models\Message;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Notifications\NewChatMessageNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    /**
     * Отримання повідомлень для проекту
     */
    public function getMessages(Project $project): JsonResponse
    {
        // Перевірка доступу до проекту
        $this->checkProjectAccess($project);
        
        $cacheKey = "project_{$project->id}_messages";
        $cacheDuration = now()->addMinutes(5); // Кешуємо на 5 хвилин (повідомлення можуть часто оновлюватися)

        $messages = Cache::remember($cacheKey, $cacheDuration, function () use ($project) {
            return Message::with('sender')
                ->byProject($project->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'message_html' => $message->message_html ?? $message->message,
                        'sender_id' => $message->sender_id,
                        'sender_name' => $message->sender->full_name,
                        'is_read' => $message->is_read,
                        'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                        'is_mine' => $message->sender_id === Auth::id(),
                    ];
                });
        });

        return response()->json(['messages' => $messages]);
    }

    /**
     * Відправлення нового повідомлення
     */
    public function sendMessage(Request $request, Project $project): JsonResponse
    {
        // Перевірка доступу до проекту
        $this->checkProjectAccess($project);

        // Валідація даних
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Створення повідомлення
        $message = Message::create([
            'project_id' => $project->id,
            'sender_id' => Auth::id(),
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        // Завантаження відправника
        $message->load('sender');

        // Підготовка даних для відповіді
        $messageData = [
            'id' => $message->id,
            'message' => $message->message,
            'message_html' => $message->message_html ?? $message->message,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->full_name,
            'is_read' => $message->is_read,
            'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            'is_mine' => $message->sender_id === Auth::id(),
        ];

        // Відправлення події для WebSocket (тільки якщо не в тестовому середовищі)
        if (!app()->environment('testing')) {
            \Log::info('Sending message via WebSocket', [
                'project_id' => $project->id,
                'message_id' => $message->id,
                'sender_id' => $message->sender_id,
                'broadcast_connection' => config('broadcasting.default')
            ]);

            broadcast(new MessageSent($project->id, $messageData));

            \Log::info('Message broadcast completed');
        }

        // Відправлення email повідомлення іншому учаснику чату
        $this->sendChatNotification($message, $project);

        // Очищення кешу повідомлень для цього проекту
        $this->clearProjectMessagesCache($project->id);

        return response()->json(['message' => $messageData]);
    }

    /**
     * Позначення повідомлень як прочитаних
     */
    public function markAsRead(Request $request, Project $project): JsonResponse
    {
        // Перевірка доступу до проекту
        $this->checkProjectAccess($project);

        // Отримання ID повідомлень для позначення
        $messageIds = $request->input('message_ids', []);

        // Позначення повідомлень як прочитаних
        $updatedCount = Message::whereIn('id', $messageIds)
            ->where('project_id', $project->id)
            ->where('sender_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        // Відправлення події про прочитання повідомлень (тільки якщо є оновлені повідомлення)
        if ($updatedCount > 0 && !app()->environment('testing')) {
            \Log::info('Broadcasting messages read status', [
                'project_id' => $project->id,
                'message_ids' => $messageIds,
                'user_id' => Auth::id(),
                'updated_count' => $updatedCount
            ]);

            broadcast(new MessagesRead($project->id, $messageIds, Auth::id()));
        }

        // Очищення кешу повідомлень для цього проекту
        $this->clearProjectMessagesCache($project->id);

        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount
        ]);
    }

    /**
     * Відправлення email повідомлення про нове повідомлення в чаті
     */
    private function sendChatNotification(Message $message, Project $project): void
    {
        try {
            // Завантажуємо необхідні зв'язки
            $project->load(['supervisor.user', 'assignedTo']);

            // Визначаємо отримувача повідомлення (не відправника)
            $recipient = null;
            $currentUserId = Auth::id();

            if ($project->supervisor && $project->supervisor->user_id !== $currentUserId) {
                // Якщо відправник не керівник, то надсилаємо керівнику
                $recipient = $project->supervisor->user;
            } elseif ($project->assignedTo && $project->assignedTo->id !== $currentUserId) {
                // Якщо відправник не студент, то надсилаємо студенту
                $recipient = $project->assignedTo;
            }

            // Відправляємо повідомлення, якщо є отримувач
            if ($recipient) {
                $recipient->notify(new NewChatMessageNotification($message));
            }
        } catch (\Exception $e) {
            // Логуємо помилку, але не зупиняємо виконання
            \Log::error("Помилка відправки повідомлення про новий чат", [
                'message_id' => $message->id,
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Очищення кешу повідомлень проекту
     */
    private function clearProjectMessagesCache(string $projectId): void
    {
        Cache::forget("project_{$projectId}_messages");
    }

    /**
     * Перевірка доступу до проекту
     */
    private function checkProjectAccess(Project $project): void
    {
        $user = Auth::user();

        // Перевірка, чи проект має призначеного студента
        if (!$project->assigned_to) {
            abort(403, 'Чат доступний тільки для проектів з призначеним студентом');
        }

        // Перевірка доступу до проекту
        if ($user->hasRole('admin')) {
            // Адміністратор має доступ до всіх проектів
            return;
        } elseif ($user->hasRole('teacher')) {
            // Викладач повинен бути керівником проекту
            if (!$project->supervisor || $project->supervisor->user_id != $user->id) {
                abort(403, 'Ви не маєте доступу до чату цього проекту, оскільки не є його науковим керівником');
            }
        } elseif ($user->hasRole('student')) {
            // Студент повинен бути призначений до проекту
            if ($project->assigned_to != $user->id) {
                abort(403, 'Ви не маєте доступу до чату цього проекту, оскільки не є призначеним студентом');
            }
        } else {
            abort(403, 'Ви не маєте доступу до чату проекту');
        }
    }
}
