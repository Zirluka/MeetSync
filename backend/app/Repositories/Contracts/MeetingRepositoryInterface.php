<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Meeting;
use Illuminate\Database\Eloquent\Collection;

interface MeetingRepositoryInterface
{
    public function findById(string $id): ?Meeting;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Meeting;

    /**
     * Загружает встречу вместе со всеми участниками и их интервалами доступности
     */
    public function findWithParticipantsAndAvailabilities(string $id): ?Meeting;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Meeting $meeting, array $attributes): bool;

    /**
     * Список встреч конкретного организатора
     * 
     * @return Collection<int, Meeting>
     */
    public function getByOrganizer(int $organizerId): Collection;
}