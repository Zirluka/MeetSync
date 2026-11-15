<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\CarbonImmutable;

final readonly class CreateMeetingDTO
{
    /**
     * @param list<string> $inviteeEmails
     */
    public function __construct(
        public string $title,
        public ?string $description,
        public int $durationMinutes,
        public CarbonImmutable $dateRangeStart,
        public CarbonImmutable $dateRangeEnd,
        public string $timezone,
        public array $inviteeEmails = [],
    )
    {}
}