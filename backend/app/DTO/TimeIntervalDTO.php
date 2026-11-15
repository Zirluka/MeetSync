<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\CarbonImmutable;

final readonly class TimeIntervalDTO
{
    public function __construct(
        public CarbonImmutable $start,
        public CarbonImmutable $end,
    )
    {}

    /**
     * Длительность в минутах
     */
    public function durationInMinutes(): int
    {
        return (int) ($this->end->diffInMinutes($this->start));
    }
}