<?php

namespace App\Enums;

enum ConType: string
{
    case Pending = 'pending';
    case Committed = 'committed';


    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

}
