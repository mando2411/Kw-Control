<?php

namespace App\Enums;

enum Type: string
{
    case MALE = 'ذكر';
    case FEMALE = 'انثى';
    case MEN = 'ذكور';
    case WOMEN = 'اناث';



    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function normalize(string $type): string
    {
        return match($type) {
            self::MALE->value => self::MEN->value,          // "ذكر" to "ذكور"
            self::FEMALE->value => self::WOMEN->value,      // "انثي" to "اناث"
            self::MEN->value => self::MALE->value,          // "ذكور" to "ذكر"
            self::WOMEN->value => self::FEMALE->value,      // "اناث" to "انثي"
                default => $type,
        };
    }

}
