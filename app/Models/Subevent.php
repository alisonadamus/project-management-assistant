<?php

namespace Alison\ProjectManagementAssistant\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Subevent extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'event_id',
        'depends_on',
        'name',
        'description',
        'start_date',
        'end_date',
        'bg_color',
        'fg_color',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function dependsOn(): BelongsTo
    {
        return $this->belongsTo(Subevent::class, 'depends_on');
    }

    public function dependentSubevents(): HasMany
    {
        return $this->hasMany(Subevent::class, 'depends_on');
    }

    public function scopeByEvent(Builder $query, string|int $eventId): Builder
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeSearchByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'LIKE', "%{$name}%");
    }

    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    public function scopeSearchByDescription(Builder $query, string $text): Builder
    {
        return $query->where('description', 'LIKE', "%{$text}%");
    }

    /**
     * Отримати HTML версію опису
     */
    protected function descriptionHtml(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->description)) {
                    return '';
                }

                $markdownService = app(\Alison\ProjectManagementAssistant\Services\MarkdownService::class);
                return $markdownService->toHtml($this->description);
            }
        );
    }

    /**
     * Отримати попередній перегляд опису
     */
    protected function descriptionPreview(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->description)) {
                    return '';
                }

                $markdownService = app(\Alison\ProjectManagementAssistant\Services\MarkdownService::class);
                return $markdownService->getPreview($this->description);
            }
        );
    }

}
