<?php

namespace Alison\ProjectManagementAssistant\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'event_id',
        'supervisor_id',
        'assigned_to',
        'slug',
        'name',
        'appendix',
        'body',
    ];
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class, 'project_technology');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'project_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'project_id');
    }

    public function scopeByEvent(Builder $query, string|int $eventId): Builder
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeBySupervisor(Builder $query, string|int $supervisorId): Builder
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    public function scopeByAssignedStudent(Builder $query, string|int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'ILIKE', "%$name%");
    }

    public function scopeWithoutSupervisor(Builder $query): Builder
    {
        return $query->whereNull('supervisor_id');
    }

    public function scopeWithSupervisor(Builder $query): Builder
    {
        return $query->whereNotNull('supervisor_id');
    }

    public function scopeWithAssignedTo(Builder $query): Builder
    {
        return $query->whereNotNull('assigned_to');
    }

    public function scopeWithoutAssignedTo(Builder $query): Builder
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeByTechnology(Builder $query, string|int $technologyId): Builder
    {
        return $query->whereHas('technologies', function ($q) use ($technologyId) {
            $q->where('technologies.id', $technologyId);
        });
    }

    public function scopeSearchByNameOrBody(Builder $query, string $search): Builder
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'ILIKE', "%$search%")
              ->orWhere('body', 'ILIKE', "%$search%");
        });
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        if ($status === 'assigned') {
            return $query->whereNotNull('assigned_to');
        } elseif ($status === 'unassigned') {
            return $query->whereNull('assigned_to');
        }

        return $query;
    }

    public function scopeOrderByName(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('name', $direction);
    }

    public function scopeOrderByCreated(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('created_at', $direction);
    }

    /**
     * Отримати HTML версію опису проекту
     */
    protected function bodyHtml(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->body)) {
                    return '';
                }

                $markdownService = app(\Alison\ProjectManagementAssistant\Services\MarkdownService::class);
                return $markdownService->toHtml($this->body);
            }
        );
    }

    /**
     * Отримати попередній перегляд опису проекту
     */
    protected function bodyPreview(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->body)) {
                    return '';
                }

                $markdownService = app(\Alison\ProjectManagementAssistant\Services\MarkdownService::class);
                return $markdownService->getPreview($this->body);
            }
        );
    }

    /**
     * Отримати кількість непрочитаних повідомлень для поточного користувача
     */
    public function getUnreadMessagesCountAttribute(): int
    {
        $currentUserId = auth()->id();
        if (!$currentUserId) {
            return 0;
        }

        return $this->messages()
            ->where('sender_id', '!=', $currentUserId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Перевірити, чи є непрочитані повідомлення для поточного користувача
     */
    public function getHasUnreadMessagesAttribute(): bool
    {
        return $this->getUnreadMessagesCountAttribute() > 0;
    }

    /**
     * Отримати кількість проектів в події
     */
    public function getEventProjectsCountAttribute(): int
    {
        if (!$this->event) {
            return 0;
        }

        return $this->event->projects()->count();
    }
}
