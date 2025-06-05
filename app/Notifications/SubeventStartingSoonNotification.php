<?php

namespace Alison\ProjectManagementAssistant\Notifications;

use Alison\ProjectManagementAssistant\Models\Subevent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubeventStartingSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Підподія, яка починається завтра
     *
     * @var \Alison\ProjectManagementAssistant\Models\Subevent
     */
    public $subevent;

    /**
     * Create a new notification instance.
     *
     * @param  \Alison\ProjectManagementAssistant\Models\Subevent  $subevent
     * @return void
     */
    public function __construct(Subevent $subevent)
    {
        $this->subevent = $subevent;
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
            ->subject('Нагадування: Підподія починається завтра - ' . $this->subevent->name)
            ->line('Шановний(а) ' . $notifiable->full_name . ',')
            ->line('Нагадуємо вам, що завтра починається підподія: **' . $this->subevent->name . '**')
            ->line('Основна подія: **' . $this->subevent->event->name . '**')
            ->line('Опис підподії: ' . ($this->subevent->description ?? 'Опис відсутній'))
            ->line('Дата початку: ' . $this->subevent->start_date->format('d.m.Y H:i'))
            ->line('Дата закінчення: ' . $this->subevent->end_date->format('d.m.Y H:i'))
            ->line('Будь ласка, підготуйтеся до участі в підподії.')
            ->action('Переглянути подію', route('events.show', $this->subevent->event))
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
            'subevent_id' => $this->subevent->id,
            'subevent_name' => $this->subevent->name,
            'event_id' => $this->subevent->event->id,
            'event_name' => $this->subevent->event->name,
            'start_date' => $this->subevent->start_date,
            'end_date' => $this->subevent->end_date,
            'type' => 'subevent_starting_soon',
        ];
    }
}
