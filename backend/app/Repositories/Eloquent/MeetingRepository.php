<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Meeting;
use App\Repositories\Contracts\MeetingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MeetingRepository implements MeetingRepositoryInterface
{
    public function findById(string $id): ?Meeting
    {
        return Meeting::query()->find($id);
    }

    public function findWithParticipantsAndAvailabilities(string $id): ?Meeting
    {
        return Meeting::query()
            ->with(['participants.availabilities', 'participants.user', 'suggestedSlots'])
            ->find($id);
    }

    public function create(array $attributes): Meeting
    {
        return Meeting::query()->create($attributes);
    }

    public function update(Meeting $meeting, array $attributes): bool
    {
        return $meeting->update($attributes);
    }

    public function getByOrganizer(int $organizerId): Collection
    {
        return Meeting::query()
            ->where('organizer_id', $organizerId)
            ->orderByDesc('created_at')
            ->get();
    }
}