<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SuggestedSlotFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuggestedSlot extends Model
{
    /** @use HasFactory<SuggestedSlotFactory> */
    use HasFactory, HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'meeting_id',
        'start_time',
        'end_time',
        'available_participants_count',
        'total_participants_count',
        'score_percentage',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'available_participants_count' => 'integer',
            'total_participants_count' => 'integer',
            'score_percentage' => 'float'
        ];
    }

    /**
     * Встреча, для которой рассчитан слот
     * 
     * @return BelongsTo<Meeting, $this>
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }
}
