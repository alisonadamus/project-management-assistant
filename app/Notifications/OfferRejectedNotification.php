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
            ->subject('Відхилено заявку на участь у проекті')
            ->greeting('Шановний(а) ' . $notifiable->name . ',')
            ->line('На жаль, вашу заявку на участь у проекті **' . $this->project->name . '** було відхилено.')
            ->line('**Керівник проекту:** ' . $this->project->supervisor->user->name)
            ->action('Переглянути інші проекти', route('projects.index'))
            ->line('Дякуємо за інтерес до нашої платформи!');
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
        ];
    }
}
