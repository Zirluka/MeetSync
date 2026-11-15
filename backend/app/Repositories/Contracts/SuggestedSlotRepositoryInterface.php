<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Meeting;
use App\Models\SuggestedSlot;
use Illuminate\Database\Eloquent\Collection;

interface SuggestedSlotRepositoryInterface
{
    public function findById(string $id): ?SuggestedSlot;

    /**
     * Сохранение новых рассчитанных слотов с удалением предыдущих
     * 
     * @param list<array{start_time: string, end_time: string, 
     * available_participants_count: int, total_participants_count: int, 
     * score_percentage: float}> $slots
     */
    public function replaceForMeeting(Meeting $meeting, array $slots): void;

    /**
     * @return Collection<int, SuggestedSlot>
     */
    public function getByMeeting(string $meetingId): Collection;
}