<?php

namespace Alison\ProjectManagementAssistant\Notifications;

use Alison\ProjectManagementAssistant\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventEndingSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Подія, яка закінчується через 2 дні
     *
     * @var Event
     */
    public $event;

    /**
     * Create a new notification instance.
     *
     * @param Event $event
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
            ->subject('Нагадування: Подія закінчується через 2 дні - ' . $this->event->name)
            ->line('Шановний(а) ' . $notifiable->full_name . ',')
            ->line('Нагадуємо вам, що через 2 дні закінчується подія: **' . $this->event->name . '**')
            ->line('Опис: ' . ($this->event->description ?? 'Опис відсутній'))
            ->line('Дата початку: ' . $this->event->start_date->format('d.m.Y H:i'))
            ->line('Дата закінчення: ' . $this->event->end_date->format('d.m.Y H:i'))
            ->line('Будь ласка, завершіть всі необхідні роботи до закінчення події.')
            ->action('Переглянути подію', route('events.show', $this->event))
            ->line('Дякуємо за участь!');
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
            'event_end_date' => $this->event->end_date->format('Y-m-d H:i:s'),
            'type' => 'event_ending_soon',
        ];
    }
}
