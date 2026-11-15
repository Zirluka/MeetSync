<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Availability;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Collection;

interface AvailabilityRepositoryInterface
{
    /**
     * Полная перезапись интервалов участника в рамках одной транзакции
     * 
     * @param list<array{start_time: string, end_time: string}> $intervals
     */
    public function replaceForParticipant(Participant $participant, array $intervals): void;

    /**
     * Все интервалы всех участников встречи для работы алгоритма
     * 
     * @return Collection<int, Availability>
     */
    public function getIntervalsByMeeting(string $meetingId): Collection;
}