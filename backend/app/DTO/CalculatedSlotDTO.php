<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\CarbonImmutable;

final readonly class CalculatedSlotDTO
{
    public function __construct(
        public CarbonImmutable $start,
        public CarbonImmutable $end,
        public int $availableCount,
        public int $totalCount,
        public float $scorePercentage,
    )
    {}
}