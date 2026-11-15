<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Meeting;
use App\Models\SuggestedSlot;
use App\Repositories\Contracts\SuggestedSlotRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Override;

class SuggestedSlotRepository implements SuggestedSlotRepositoryInterface
{
    public function findById(string $id): ?SuggestedSlot
    {
        return SuggestedSlot::query()->find($id);        
    }

    public function replaceForMeeting(Meeting $meeting, array $slots): void
    {
        DB::transaction(function () use ($meeting, $slots): void {
            $meeting->suggestedSlots()->delete();

            foreach ($slots as $slot) {
                $meeting->suggestedSlots()->create($slot);
            }
        });        
    }

    public function getByMeeting(string $meetingId): Collection
    {
        return SuggestedSlot::query()
            ->where('meeting_id', $meetingId)
            ->orderByDesc('score_percentage')
            ->orderBy('start_time')
            ->get();
    }
}