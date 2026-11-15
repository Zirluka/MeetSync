<?php

declare(strict_types=1);

use App\Models\Availability;
use App\Models\Meeting;
use App\Models\Participant;
use App\Services\SlotCalculationService;
use Carbon\CarbonImmutable;

it('finds 100 percent overlapping slot for multiple participants', function (): void {
    $service = new SlotCalculationService();

    $meeting = new Meeting([
        'duration_minutes' => 60,
        'date_range_start' => CarbonImmutable::parse('2026-11-20 09:00:00'),
        'date_range_end' => CarbonImmutable::parse('2026-11-20 18:00:00'),
    ]);

    // Участник 1 свободен: 10:00 - 14:00
    $p1 = new Participant(['id' => 'p1']);
    $p1->setRelation('availabilities', collect([
        new Availability([
            'start_time' => CarbonImmutable::parse('2026-11-20 10:00:00'),
            'end_time' => CarbonImmutable::parse('2026-11-20 14:00:00'),
        ]),
    ]));

    // Участник 2 свободен: 11:00 - 15:00
    $p2 = new Participant(['id' => 'p2']);
    $p2->setRelation('availabilities', collect([
        new Availability([
            'start_time' => CarbonImmutable::parse('2026-11-20 11:00:00'),
            'end_time' => CarbonImmutable::parse('2026-11-20 15:00:00'),
        ]),
    ]));

    $meeting->setRelation('participants', collect([$p1, $p2]));

    // Запускаем расчет
    $slots = $service->calculateForMeeting($meeting, stepMinutes: 30);

    // Должны найтись слоты
    expect($slots)->not->toBeEmpty();

    // Первый (топовый) слот должен иметь 100% явку (оба участника свободны с 11:00 до 12:00)
    $bestSlot = $slots[0];
    expect($bestSlot->scorePercentage)->toBe(100.0)
        ->and($bestSlot->availableCount)->toBe(2)
        ->and($bestSlot->start->format('H:i'))->toBe('11:00')
        ->and($bestSlot->end->format('H:i'))->toBe('12:00');
});

it('correctly ranks partial availability when full overlap is impossible', function (): void {
    $service = new SlotCalculationService();

    $meeting = new Meeting([
        'duration_minutes' => 60,
        'date_range_start' => CarbonImmutable::parse('2026-11-20 09:00:00'),
        'date_range_end' => CarbonImmutable::parse('2026-11-20 15:00:00'),
    ]);

    // Участник 1: 10:00 - 12:00
    $p1 = new Participant(['id' => 'p1']);
    $p1->setRelation('availabilities', collect([
        new Availability([
            'start_time' => CarbonImmutable::parse('2026-11-20 10:00:00'),
            'end_time' => CarbonImmutable::parse('2026-11-20 12:00:00'),
        ]),
    ]));

    // Участник 2: 10:00 - 12:00
    $p2 = new Participant(['id' => 'p2']);
    $p2->setRelation('availabilities', collect([
        new Availability([
            'start_time' => CarbonImmutable::parse('2026-11-20 10:00:00'),
            'end_time' => CarbonImmutable::parse('2026-11-20 12:00:00'),
        ]),
    ]));

    // Участник 3: только 13:00 - 15:00 (не пересекается с p1 и p2)
    $p3 = new Participant(['id' => 'p3']);
    $p3->setRelation('availabilities', collect([
        new Availability([
            'start_time' => CarbonImmutable::parse('2026-11-20 13:00:00'),
            'end_time' => CarbonImmutable::parse('2026-11-20 15:00:00'),
        ]),
    ]));

    $meeting->setRelation('participants', collect([$p1, $p2, $p3]));

    $slots = $service->calculateForMeeting($meeting, stepMinutes: 30);

    // Максимальная явка может быть только 2 из 3 = 66.67%
    expect($slots[0]->scorePercentage)->toBe(66.67)
        ->and($slots[0]->availableCount)->toBe(2)
        ->and($slots[0]->totalCount)->toBe(3);
});