<?php

namespace Alison\ProjectManagementAssistant\Models;

use Database\Factories\OfferFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    /** @use HasFactory<OfferFactory> */
    use HasFactory;

    protected $primaryKey = ['project_id', 'student_id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'student_id',
        'project_id',
        'created_at',
        'updated_at',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function scopeByStudent(Builder $query, string|int $studentId): Builder
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByProject(Builder $query, string|int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeLatestOffers(Builder $query, int $limit = 5): Builder
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', now()->toDateString());
    }



}
