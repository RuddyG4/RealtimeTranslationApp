<?php

namespace App\Enums;

enum UserState: int
{
    case OFFLINE  = 0;
    case ONLINE = 1;
    case BUSY = 2;
    case IDLE = 3;
    case IN_A_MEETING = 4;

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
