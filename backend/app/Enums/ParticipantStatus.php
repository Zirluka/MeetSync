<?php

declare(strict_types=1);

namespace App\Enums;

enum ParticipantStatus: string 
{
    case Invited = 'invited';     //  Приглашение отправлено, ждет ответа
    case Responded = 'responded'; //  Участник заполнил свои интервалы доступности
    case Declined = 'declined';   //  Участник отклонил приглашение

    public function label(): string
    {
        return match ($this) {
            self::Invited => 'Ожидает ответа',
            self::Responded => 'Доступность указана',
            self::Declined => 'Отклонено'
        };
    }
}