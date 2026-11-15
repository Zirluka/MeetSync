<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ParticipantStatus;
use Database\Factories\ParticipantFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Participant extends Model
{
    /** @use HasFactory<ParticipantFactory> */
    use HasFactory, HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'meeting_id',
        'user_id',
        'guest_name',
        'guest_email',
        'access_token',
        'status',
        'responded_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ParticipantStatus::class,
            'responded_at' => 'datetime'
        ];
    }

    /**
     * Встреча, к которой прикреплен участник
     * 
     * @return BelongsTo<Meeting, $this>
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    /**
     * Зарегистрированный пользователь (null, если гость)
     * 
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Временные интервалы доступности участника
     * 
     * @return HasMany<Availability, $this>
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    /**
     * Является ли участник гостем (без аккаунта)
     */
    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    /**
     * Отображаемое имя (из профиля или гостевое)
     */
    public function displayName(): string
    {
        return $this->user->name ?? $this->guest_name ?? 'Гость';
    }

    /**
     * Email участника (может быть null, если гость не указал почту)
     */
    public function displayEmail(): ?string
    {
        return $this->user->email ?? $this->guest_email;
    }
}
