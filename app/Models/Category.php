<?php

namespace Alison\ProjectManagementAssistant\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'freezing_period',
        'course_number',
        'period',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'category_subject');
    }

    public function scopeByCourseNumber(Builder $query, int $courseNumber): Builder
    {
        return $query->where('course_number', $courseNumber);
    }

    public function scopeSearchByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'LIKE', "%{$name}%");
    }

    public function scopeWithSubject(Builder $query, string|int $subjectId): Builder
    {
        return $query->whereHas('subjects', function ($q) use ($subjectId) {
            $q->where('subjects.id', $subjectId);
        });
    }

    public function scopeMinFreezingPeriod(Builder $query, int $days): Builder
    {
        return $query->where('freezing_period', '>=', $days);
    }

    public function scopeMaxFreezingPeriod(Builder $query, int $days): Builder
    {
        return $query->where('freezing_period', '<=', $days);
    }

    public function scopeMinPeriod(Builder $query, int $days): Builder
    {
        return $query->where('period', '>=', $days);
    }

    public function scopeMaxPeriod(Builder $query, int $days): Builder
    {
        return $query->where('period', '<=', $days);
    }

}
