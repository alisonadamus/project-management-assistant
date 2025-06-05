<?php

namespace Alison\ProjectManagementAssistant\Models;

use Database\Factories\SubjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Subject extends Model
{
    /** @use HasFactory<SubjectFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'slug',
        'name',
        'course_number',
        'description',
        'image',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_subject');
    }

    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'ILIKE', "%$name%");
    }

    public function scopeByCourse(Builder $query, int $course): Builder
    {
        return $query->where('course_number', $course);
    }

    public function scopeSearchByDescription(Builder $query, string $text): Builder
    {
        return $query->where('description', 'LIKE', "%{$text}%");
    }

    public function scopeSearchByName(Builder $query, string $text): Builder
    {
        return $query->where('name', 'LIKE', "%{$text}%");
    }

    public function scopeByCategory(Builder $query, string|int $categoryId): Builder
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
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
