<?php
declare(strict_types=1);

namespace App\Models;

enum ReceptionRepetitive_repetitivityKinds : int
{
    case EVEN = 1;
    case ODD  = 2;
    case ALL  = 3;

    /**
     * is even?
     */
    public function isEven() : bool
    {
        return $this === self::EVEN;
    }

    /**
     * is odd?
     */
    public function isOdd() : bool
    {
        return $this === self::ODD;
    }

    /**
     * is all?
     */
    public function isAll() : bool
    {
        return $this === self::ALL;
    }

    /**
     * get as string
     */
    public function getAsString() : string
    {
        return match($this)
        {
            self::EVEN => 'páros hét',
            self::ODD  => 'páratlan hét',
            self::ALL  => 'összes hét',
            default    => throw new \RuntimeException('Ismeretlen típus!')
        };
    }
}
