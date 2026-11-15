<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\CalculatedSlotDTO;
use App\Models\Meeting;
use App\Models\Participant;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class SlotCalculationService
{
    /**
     * Рассчитывает оптимальные слоты для встречи
     * 
     * @param int $stepMinutes Шаг генерации кандидатов (по умолчанию 30 мин)
     * @return list<CalculatedSlotDTO>
     */
    public function calculateForMeeting(Meeting $meeting, int $stepMinutes = 30): array
    {
        $duration = $meeting->duration_minutes;
        $rangeStart = CarbonImmutable::parse((string) $meeting->date_range_start);
        $rangeEnd = CarbonImmutable::parse((string) $meeting->date_range_end);

        /** @var Collection<int, Participant> $participants */
        $participants = $meeting->participants;
        $totalParticipants = $participants->count();

        if ($totalParticipants === 0) {
            return [];
        }

        // 1. Нормализуем и объединяем смежные интервалы для каждого участника
        $participantIntervals = [];
        foreach ($participants as $participant) {
            $rawIntervals = [];
            foreach ($participant->availabilities as $availability) {
                $rawIntervals[] = [
                    'start' => CarbonImmutable::parse((string) $availability->start_time),
                    'end' => CarbonImmutable::parse((string) $availability->end_time),
                ];
            }
            $participantIntervals[] = $this->mergeContiguousIntervals($rawIntervals);
        }

        // 2. Генерируем кандидатов с заданным шагом сетки внутри допустимого диапазона
        $candidates = [];
        $currentStart = $rangeStart;

        while ($currentStart->addMinutes($duration)->lessThanOrEqualTo($rangeEnd)) {
            $currentEnd = $currentStart->addMinutes($duration);

            // Считаем, сколько участников полностью свободны на отрезке [currentStart, currentEnd]
            $availableCount = 0;
            foreach ($participantIntervals as $intervals) {
                if ($this->isParticipantAvailableThroughout($intervals, $currentStart, $currentEnd)) {
                    $availableCount++;
                }
            }

            if ($availableCount > 0) {
                $scorePercentage = round(($availableCount / $totalParticipants) * 100, 2);

                $candidates[] = new CalculatedSlotDTO(
                    start: $currentStart,
                    end: $currentEnd,
                    availableCount: $availableCount,
                    totalCount: $totalParticipants,
                    scorePercentage: $scorePercentage
                );
            }

            $currentStart = $currentStart->addMinutes($stepMinutes);
        }

        // 3. Сортируем: сначала максимальный процент явки, затем самое раннее время
        usort($candidates, function (CalculatedSlotDTO $a, CalculatedSlotDTO $b): int {
            if ($b->scorePercentage !== $a->scorePercentage) {
                return $b->scorePercentage <=> $a->scorePercentage;
            }

            return $a->start->getTimestamp() <=> $b->start->getTimestamp();
        });

        return $candidates;
    }

    /**
     * Склеивает пересекающиеся и примыкающие интервалы одного участника
     * 
     * @param list<array{start: CarbonImmutable, end: CarbonImmutable}> $intervals
     * @return list<array{start: CarbonImmutable, end: CarbonImmutable}>
     */
    private function mergeContiguousIntervals(array $intervals): array
    {
        if (count($intervals) <= 1) {
            return $intervals;
        }

        // Сортируем по времени старта
        usort($intervals, fn(array $a, array $b): int => $a['start']->getTimestamp() <=> $b['start']->getTimestamp());
        
        $merged = [$intervals[0]];

        for ($i = 1, $count = count($intervals); $i < $count; $i++) {
            $lastIndex = count($merged) - 1;
            $current = $intervals[$i];

            // Если текущий интервал начинается до или в момент окончания предыдущего
            if ($current['start']->lessThanOrEqualTo($merged[$lastIndex]['end'])) {
                if ($current['end']->greaterThan($merged[$lastIndex]['end'])) {
                    $merged[$lastIndex]['end'] = $current['end'];
                }
            } else {
                $merged[] = $current;
            }
        }
        
        return $merged;
    }

    /**
     * Проверяет, покрывает ли хотя бы один интервал участника целевой отрезок целиком
     * 
     * @param list<array{start: CarbonImmutable, end: CarbonImmutable}> $intervals
     */
    private function isParticipantAvailableThroughout(
        array $intervals, 
        CarbonImmutable $slotStart, 
        CarbonImmutable $slotEnd
    ): bool
    {
        foreach ($intervals as $interval) {
            if ($interval['start']->lessThanOrEqualTo($slotStart) && $interval['end']->greaterThanOrEqualTo($slotEnd)) {
                return true;
            }
        }

        return false;
    }
}