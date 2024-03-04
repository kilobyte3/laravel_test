<?php
declare(strict_types=1);

namespace App\Models;

enum ReceptionRepetitive_repetitivitydays : int
{
    case MONDAY    = 1;
    case TUESDAY   = 2;
    case WEDNESDAY = 3;
    case THURSDAY  = 4;
    case FRIDAY    = 5;
    case SATURDAY  = 6;
    case SUNDAY    = 7;

    /**
     * get keys
     */
    public static function getKeys() : array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * get as string
     */
    public function getAsString() : string
    {
        return match($this)
        {
            self::MONDAY    => 'hétfő',
            self::TUESDAY   => 'kedd',
            self::WEDNESDAY => 'szerda',
            self::THURSDAY  => 'csütörtök',
            self::FRIDAY    => 'péntek',
            self::SATURDAY  => 'szombat',
            self::SUNDAY    => 'vasárnap',
            default         => throw new \RuntimeException('Ismeretlen típus!')
        };
    }
}
