<?php

namespace Alison\ProjectManagementAssistant\Notifications;

use Alison\ProjectManagementAssistant\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOfferNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Заявка від студента
     *
     * @var \Alison\ProjectManagementAssistant\Models\Offer
     */
    public $offer;

    /**
     * Create a new notification instance.
     *
     * @param  \Alison\ProjectManagementAssistant\Models\Offer  $offer
     * @return void
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
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
            ->subject('Нова заявка на проект: ' . $this->offer->project->name)
            ->line('Шановний(а) ' . $notifiable->full_name . ',')
            ->line('Ви отримали нову заявку на участь у проекті.')
            ->line('**Проект:** ' . $this->offer->project->name)
            ->line('**Студент:** ' . $this->offer->student->full_name)
            ->line('**Email студента:** ' . $this->offer->student->email)
            ->line('**Курс:** ' . $this->offer->student->course_number)
            ->line('**Подія:** ' . $this->offer->project->event->name)
            ->line('**Дата подачі заявки:** ' . $this->offer->created_at->format('d.m.Y H:i'))
            ->action('Переглянути заявки', route('teacher.offers.index'))
            ->line('Будь ласка, розгляньте заявку та прийміть рішення.');
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
            'offer_id' => $this->offer->id ?? null,
            'project_id' => $this->offer->project->id,
            'project_name' => $this->offer->project->name,
            'student_id' => $this->offer->student->id,
            'student_name' => $this->offer->student->name,
            'student_email' => $this->offer->student->email,
            'event_name' => $this->offer->project->event->name,
            'type' => 'new_offer',
        ];
    }
}
