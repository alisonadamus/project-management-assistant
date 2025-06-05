<?php

namespace Alison\ProjectManagementAssistant\Notifications;

use Alison\ProjectManagementAssistant\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Проект, на який було відхилено заявку
     *
     * @var Project
     */
    public $project;

    /**
     * Причина відхилення заявки
     *
     * @var string
     */
    public $reason;

    /**
     * Create a new notification instance.
     *
     * @param Project $project
     * @param string $reason
     * @return void
     */
    public function __construct(Project $project, string $reason = 'manual')
    {
        $this->project = $project;
        $this->reason = $reason;
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
        $reasonText = $this->getReasonText();

        return (new MailMessage)
            ->subject('Відхилено заявку на участь у проекті - ' . $this->project->name)
            ->greeting('Шановний(а) ' . $notifiable->full_name . ',')
            ->line('На жаль, вашу заявку на участь у проекті **' . $this->project->name . '** було відхилено.')
            ->line('**Причина:** ' . $reasonText)
            ->line('**Керівник проекту:** ' . $this->project->supervisor->user->full_name)
            ->line('**Подія:** ' . $this->project->event->name)
            ->action('Переглянути інші проекти', route('events.show', $this->project->event))
            ->line('Дякуємо за інтерес до нашої платформи!');
    }

    /**
     * Отримання тексту причини відхилення
     */
    private function getReasonText(): string
    {
        return match($this->reason) {
            'manual' => 'Заявку відхилено науковим керівником',
            'other_student_approved' => 'На цей проект було призначено іншого студента',
            'student_assigned_elsewhere' => 'Ви були призначені до іншого проекту в цій події',
            'supervisor_slots_full' => 'У наукового керівника закінчилися вільні місця',
            default => 'Заявку відхилено'
        };
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
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'supervisor_name' => $this->project->supervisor->user->name,
            'event_name' => $this->project->event->name,
            'reason' => $this->reason,
            'reason_text' => $this->getReasonText(),
            'type' => 'offer_rejected',
        ];
    }
}
