<?php

declare(strict_types=1);

namespace App\Enums;

enum MeetingStatus: string 
{
    case Draft = 'draft';           //  Черновик
    case Collecting = 'collecting'; //  Идет сбор доступности от участников
    case Confirmed = 'confirmed';   //  Стол выбран и утвержден
    case Cancelled = 'cancelled';   //  Встреча отменена

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Черновик',
            self::Collecting => 'Сбор доступности',
            self::Confirmed => 'Подтверждена',
            self::Cancelled => 'Отменена'
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Confirmed, self::Cancelled], true);
    }
}