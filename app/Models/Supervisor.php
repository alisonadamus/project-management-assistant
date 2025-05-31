<?php

namespace Alison\ProjectManagementAssistant\Models;

use Database\Factories\SupervisorFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supervisor extends Model
{
    /** @use HasFactory<SupervisorFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'event_id',
        'user_id',
        'note',
        'slot_count',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'supervisor_id');
    }

    public function scopeByEvent(Builder $query, string|int $eventId): Builder
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeByUser(Builder $query, string|int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSearchByNote(Builder $query, string $text): Builder
    {
        return $query->where('note', 'LIKE', "%{$text}%");
    }

    public function scopeActiveEvent(Builder $query): Builder
    {
        return $query->whereHas('event', function ($q) {
            $q->where('end_date', '>=', now());
        });
    }

    public function scopeBySlotCount(Builder $query, int $slotCount): Builder
    {
        return $query->where('slot_count', $slotCount);
    }

    public function scopeMinSlotCount(Builder $query, int $slots): Builder
    {
        return $query->where('slot_count', '>=', $slots);
    }

    public function scopeMaxSlotCount(Builder $query, int $slots): Builder
    {
        return $query->where('slot_count', '<=', $slots);
    }



}
