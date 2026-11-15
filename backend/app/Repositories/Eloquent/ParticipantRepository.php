<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\ParticipantStatus;
use App\Models\Participant;
use App\Repositories\Contracts\ParticipantRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ParticipantRepository implements ParticipantRepositoryInterface
{
    public function findById(string $id): ?Participant
    {
        return Participant::query()->find($id);
    }

    public function findByAccessToken(string $token): ?Participant
    {
        return Participant::query()
            ->with(['meeting', 'user'])
            ->where('access_token', $token)
            ->first();
    }

    public function create(array $attributes): Participant
    {
        return Participant::query()->create($attributes);
    }

    public function updateStatus(Participant $participant, ParticipantStatus $status): bool
    {
        return $participant->update([
            'status' => $status,
            'responded_at' => now()
        ]);
    }

    public function getByMeeting(string $meetingId): Collection
    {
        return Participant::query()
            ->where('meeting_id', $meetingId)
            ->with('user')
            ->get();
    }
}