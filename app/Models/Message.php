<?php

namespace Alison\ProjectManagementAssistant\Models;

use Database\Factories\ProjectFactory;
use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    /** @use HasFactory<MessageFactory> */
    use HasFactory, HasUlids;
    protected $fillable = [
        'project_id',
        'sender_id',
        'message',
        'is_read',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function scopeByProject(Builder $query, string|int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeBySender(Builder $query, string|int $senderId): Builder
    {
        return $query->where('sender_id', $senderId);
    }

    public function scopeByIsRead(Builder $query, bool $isRead): Builder
    {
        return $query->where('is_read', $isRead);
    }
}
