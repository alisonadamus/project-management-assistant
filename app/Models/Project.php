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



}
