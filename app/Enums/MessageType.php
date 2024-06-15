<?php

namespace App\Enums;

enum MessageType: int
{
    case TEXT  = 0;
    case AUDIO = 1;
    case IMAGE = 2;
    case VIDEO = 3;
    case FILE = 4;

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
