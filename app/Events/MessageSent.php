<?php

namespace Alison\ProjectManagementAssistant\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * ID проекту
     *
     * @var string
     */
    public $projectId;

    /**
     * Дані повідомлення
     *
     * @var array
     */
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(string $projectId, array $message)
    {
        $this->projectId = $projectId;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('project.' . $this->projectId),
        ];
    }

    /**
     * Назва події для клієнта
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
