<?php

namespace Alison\ProjectManagementAssistant\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasUlids;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'google_id',
        'first_name',
        'last_name',
        'middle_name',
        'description',
        'avatar',
        'course_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'full_name',
        'short_name',
        'name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function supervisors(): HasMany
    {
        return $this->hasMany(Supervisor::class, 'supervisors');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'assigned_to');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'student_id');
    }

    public function scopeByRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where(function($q) use ($name) {
            $q->where('first_name', 'like', '%' . $name . '%')
              ->orWhere('last_name', 'like', '%' . $name . '%')
              ->orWhere('middle_name', 'like', '%' . $name . '%');
        });
    }

    public function scopeByCourse(Builder $query, int $course): Builder
    {
        return $query->where('course_number', $course);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeByEmailDomain(Builder $query, string $domain): Builder
    {
        return $query->where('email', 'like', '%' . $domain);
    }

    public function scopeIsSupervisor(Builder $query): Builder
    {
        return $query->whereHas('supervisors');
    }

    public function scopeHasOffers(Builder $query): Builder
    {
        return $query->whereHas('offers');
    }

    public function scopeAlphabetically(Builder $query): Builder
    {
        return $query->orderBy('last_name')->orderBy('first_name');
    }

    public function scopeRecentFirst(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeLimitUsers(Builder $query, int $limit): Builder
    {
        return $query->limit($limit);
    }

    /**
     * Отримати повне ім'я користувача
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->last_name,
            $this->first_name,
            $this->middle_name,
        ]);

        return implode(' ', $parts);
    }

    /**
     * Отримати ім'я користувача (для сумісності з Filament)
     */
    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Отримати коротке ім'я користувача (прізвище та ініціали)
     */
    public function getShortNameAttribute(): string
    {
        $parts = [$this->last_name];

        if ($this->first_name) {
            $parts[] = mb_substr($this->first_name, 0, 1) . '.';
        }

        if ($this->middle_name) {
            $parts[] = mb_substr($this->middle_name, 0, 1) . '.';
        }

        return implode(' ', $parts);
    }

    /**
     * Отримати HTML версію опису користувача
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
     * Отримати попередній перегляд опису користувача
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
