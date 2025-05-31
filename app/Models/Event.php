<?php

namespace Alison\ProjectManagementAssistant\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'start_date',
        'end_date',
        'bg_color',
        'fg_color',
        'image',
        'created_at',
        'updated_at',
    ];

    protected $appends = ['image_url'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function supervisors(): HasMany
    {
        return $this->hasMany(Supervisor::class, 'event_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'event_id');
    }

    public function scopeByCategory(Builder $query, string|int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('end_date', '>=', now());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeSearchByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'LIKE', "%{$name}%");
    }

    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    /**
     * Get the full URL for the event image.
     *
     * @return string|null
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        // Якщо це вже повний URL, повертаємо як є
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // Видаляємо початковий слеш, якщо він є
        $path = ltrim($this->image, '/');

        // Якщо шлях вже містить 'storage/', додаємо лише початковий слеш
        if (str_starts_with($path, 'storage/')) {
            return '/' . $path;
        }

        // В іншому випадку додаємо 'storage/' на початок
        return '/storage/' . $path;
    }

}
