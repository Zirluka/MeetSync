<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class SubmitAvailabilityDTO
{
    /**
     * @param list<TimeIntervalDTO> $intervals
     */
    public function __construct(
        public string $participantToken,
        public ?string $guestName,
        public ?string $guestEmail,
        public array $intervals,
    )
    {}
}