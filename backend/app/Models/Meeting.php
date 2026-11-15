<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MeetingStatus;
use Database\Factories\MeetingFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    /** @use HasFactory<MeetingFactory> */
    use HasFactory, HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'organizer_id',
        'title',
        'description',
        'duration_minutes',
        'date_range_start',
        'date_range_end',
        'timezone',
        'status',
        'confirmed_slot_id'
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'date_range_start' => 'datetime',
            'date_range_end' => 'datetime',
            'status' => MeetingStatus::class
        ];
    }

    /**
     * Организатор встречи
     * 
     * @return BelongsTo<User, $this>
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Список участников
     * 
     * @return HasMany<SuggestedSlot, $this>
     */
    public function suggestedSlots(): HasMany
    {
        return $this->hasMany(SuggestedSlot::class);
    }

    /**
     * Выбранный финальный слот встречи
     * 
     * @return BelongsTo<SuggestedSlot, $this>
     */
    public function confirmedSlot(): BelongsTo
    {
        return $this->belongsTo(SuggestedSlot::class, 'confirmed_slot_id');
    }
}
