<?php

namespace Alison\ProjectManagementAssistant\Notifications;

use Alison\ProjectManagementAssistant\Models\Message;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewChatMessageNotification extends Notification
{
    use Queueable;

    /**
     * Повідомлення в чаті
     *
     * @var Message
     */
    public $message;

    /**
     * Проект, до якого належить повідомлення
     *
     * @var Project
     */
    public $project;

    /**
     * Відправник повідомлення
     *
     * @var User
     */
    public $sender;

    /**
     * Create a new notification instance.
     *
     * @param Message $message
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->project = $message->project;
        $this->sender = $message->sender;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = ['mail'];

        // Додаємо webpush канал якщо користувач має активні підписки
        if ($notifiable->pushSubscriptions()->exists()) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $messagePreview = strlen($this->message->message) > 100 
            ? substr($this->message->message, 0, 100) . '...' 
            : $this->message->message;

        return (new MailMessage)
            ->subject('Нове повідомлення в чаті проекту: ' . $this->project->name)
            ->line('Шановний(а) ' . $notifiable->full_name . ',')
            ->line('Ви отримали нове повідомлення в чаті проекту.')
            ->line('**Проект:** ' . $this->project->name)
            ->line('**Від:** ' . $this->sender->full_name)
            ->line('**Повідомлення:** ' . $messagePreview)
            ->line('**Подія:** ' . $this->project->event->name)
            ->line('**Дата відправлення:** ' . $this->message->created_at->format('d.m.Y H:i'))
            ->action('Переглянути чат', route('projects.show', $this->project))
            ->line('Будь ласка, перевірте повідомлення та дайте відповідь за потреби.');
    }

    /**
     * Get the web push representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush($notifiable)
    {
        $messagePreview = strlen($this->message->message) > 80
            ? substr($this->message->message, 0, 80) . '...'
            : $this->message->message;

        return (new WebPushMessage())
            ->title('💬 Нове повідомлення в чаті')
            ->body($this->sender->full_name . ': ' . $messagePreview)
            ->icon('/favicon.ico')
            ->badge('/favicon.ico')
            ->action('Переглянути', route('projects.show', $this->project))
            ->data([
                'project_id' => $this->project->id,
                'project_name' => $this->project->name,
                'sender_id' => $this->sender->id,
                'sender_name' => $this->sender->full_name,
                'message_id' => $this->message->id,
                'event_name' => $this->project->event->name,
                'type' => 'new_chat_message',
                'url' => route('projects.show', $this->project)
            ])
            ->tag('chat-' . $this->project->id)
            ->requireInteraction(false)
            ->vibrate([200, 100, 200]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message_id' => $this->message->id,
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->full_name,
            'message_preview' => strlen($this->message->message) > 100 
                ? substr($this->message->message, 0, 100) . '...' 
                : $this->message->message,
            'event_name' => $this->project->event->name,
            'type' => 'new_chat_message',
        ];
    }
}
