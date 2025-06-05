<?php

namespace Alison\ProjectManagementAssistant\Notifications;

use Alison\ProjectManagementAssistant\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Проект, на який було затверджено заявку
     *
     * @var Project
     */
    public $project;

    /**
     * Create a new notification instance.
     *
     * @param Project $project
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
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
            ->subject('Вашу заявку затверджено - ' . $this->project->name)
            ->greeting('Вітаємо, ' . $notifiable->full_name . '!')
            ->line('Вашу заявку на участь у проекті **' . $this->project->name . '** було успішно затверджено.')
            ->line('Ви тепер призначені до цього проекту!')
            ->line('**Керівник проекту:** ' . $this->project->supervisor->user->full_name)
            ->line('**Email керівника:** ' . $this->project->supervisor->user->email)
            ->line('**Подія:** ' . $this->project->event->name)
            ->action('Переглянути проект', route('projects.show', $this->project))
            ->line('Дякуємо за участь у нашому проекті!');
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
            'supervisor_email' => $this->project->supervisor->user->email,
            'event_name' => $this->project->event->name,
            'type' => 'offer_approved',
        ];
    }
}
