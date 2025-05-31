<?php

namespace Alison\ProjectManagementAssistant\Notifications;

use Alison\ProjectManagementAssistant\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventStartNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Подія, яка починається
     *
     * @var \Alison\ProjectManagementAssistant\Models\Event
     */
    public $event;

    /**
     * Create a new notification instance.
     *
     * @param  \Alison\ProjectManagementAssistant\Models\Event  $event
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Початок події: ' . $this->event->name)
            ->line('Шановний(а) ' . $notifiable->name . ',')
            ->line('Повідомляємо вас, що сьогодні починається подія: ' . $this->event->name)
            ->line('Опис: ' . ($this->event->description ?? 'Опис відсутній'))
            ->line('Дата початку: ' . $this->event->start_date->format('d.m.Y H:i'))
            ->line('Дата закінчення: ' . $this->event->end_date->format('d.m.Y H:i'))
            ->line('Дякуємо, що ви з нами!');
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
            'event_id' => $this->event->id,
            'event_name' => $this->event->name,
            'start_date' => $this->event->start_date,
            'end_date' => $this->event->end_date,
        ];
    }
}
