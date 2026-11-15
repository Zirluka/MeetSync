<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Availability;
use App\Models\Participant;
use App\Repositories\Contracts\AvailabilityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AvailabilityRepository implements AvailabilityRepositoryInterface
{
    public function replaceForParticipant(Participant $participant, array $intervals): void
    {
        DB::transaction(function () use ($participant, $intervals): void {
            $participant->availabilities()->delete();

            foreach ($intervals as $interval) {
                $participant->availabilities()->create($interval);
            }
        });
    }

    public function getIntervalsByMeeting(string $meetingId): Collection
    {
        return Availability::query()
            ->whereHas('participant', function ($query) use ($meetingId): void {
                $query->where('meeting_id', $meetingId);
            })
            ->with('participant')
            ->orderBy('start_time')
            ->get();
    }
}