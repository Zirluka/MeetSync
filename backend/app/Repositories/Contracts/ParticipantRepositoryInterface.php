<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\ParticipantStatus;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Collection;

interface ParticipantRepositoryInterface
{
    public function findById(string $id): ?Participant;

    /**
     * Поиск участника по уникальному защищенному токену доступа
     */
    public function findByAccessToken(string $token): ?Participant;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Participant;

    public function updateStatus(Participant $participant, ParticipantStatus $status): bool;

    /**
     * @return Collection<int, Participant>
     */
    public function getByMeeting(string $meetingId): Collection;
}