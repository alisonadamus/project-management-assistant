<?php

namespace Alison\ProjectManagementAssistant\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * ID проекту
     *
     * @var string
     */
    public $projectId;

    /**
     * ID повідомлень, які були прочитані
     *
     * @var array
     */
    public $messageIds;

    /**
     * ID користувача, який прочитав повідомлення
     *
     * @var string
     */
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $projectId, array $messageIds, string $userId)
    {
        $this->projectId = $projectId;
        $this->messageIds = $messageIds;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        \Log::info('Broadcasting messages read status', [
            'channel' => 'project.' . $this->projectId,
            'message_ids' => $this->messageIds,
            'user_id' => $this->userId
        ]);
        
        return [
            new PrivateChannel('project.' . $this->projectId),
        ];
    }

    /**
     * Назва події для клієнта
     */
    public function broadcastAs(): string
    {
        return 'messages.read';
    }

    /**
     * Дані для відправки клієнту
     */
    public function broadcastWith(): array
    {
        return [
            'message_ids' => $this->messageIds,
            'user_id' => $this->userId
        ];
    }
}
