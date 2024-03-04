<?php
declare(strict_types=1);

namespace App\Library;

/**
 * az ismétlődő dátumok léptetése egy megadott dátumig
 *
 * az osztály működése teljesen szembemegy az immutabilitás szabályaival,
 * de így egyszerűsödik a belső szerkezete, nem kell sok paramétert átadni a függvényeknek
 */
final class RepetitiveDateToConcreteDate
{
    private \DateTimeImmutable $until;
    private int $sourceIndex;
    private array $item;
    private array $output;

    /**
     * do it
     *
     * @param $dates - dates
     * @param $until - until
     *
     * @return DateTimeRanges[]
     */
    public function doIt(array $dates, \DateTimeImmutable $until) : array
    {
        $this->until = $until;
        $this->output = [];
        foreach($dates as $this->sourceIndex => $this->item)
        {
            $this->handleDateEntry();
        }
        return $this->output;
    }

    /**
     * handle date entry
     */
    private function handleDateEntry() : void
    {
        if ($this->item['repetitivity'] === null)
        {
            $this->output[$this->sourceIndex] = $this->item['range'];
        }
        else
        {
            $this->handleRepetitivity();
        }
    }

    /**
     * handle repetitivity
     */
    private function handleRepetitivity() : void
    {
        $start = \DateTime::createFromImmutable($this->item['range']->getStart());
        $end = \DateTime::createFromImmutable($this->item['range']->getEnd());
        $to = $this->until;
        // innentől a $start és $end dátumokat 1-1 nappal a jövőbe léptetjük, a megadott dátumig
        // (ez vagy a foglalandó dátum, vagy az ügyfélfogadási dátumtartomány vége, ha meg van adva)
        do {
            // a kód úgy gondolkodik, hogy alapesetben hozzáadna dátumot, amíg egy feltétel ezt meg nem akadályozza
            if ($this->doAdd($start))
            {
                $this->output[$this->sourceIndex] = new DateTimeRanges(\DateTimeImmutable::createFromMutable($start), \DateTimeImmutable::createFromMutable($end));
            }
            $start->add(new \DateInterval('P1D'));
            $end->add(new \DateInterval('P1D'));
            if (is_object($this->item['until']) && $end > $this->item['until'])
            {
                break;
            }
        } while($start < $to);
    }

    /**
     * do add?
     *
     * @param $date - date
     */
    private function doAdd(\DateTime $date) : bool
    {
        if ((int)$date->format('N') !== $this->item['repetitivityday']->value)
        {
            return false;
        }

        // páratlan és páros héten vagyunk?
        if ($this->item['repetitivity']->isOdd() /** páratlan */ && $date->format('W') % 2 === 0 /** páros */)
        {
            return false;
        }

        // páros és páratlan héten vagyunk?
        if ($this->item['repetitivity']->isEven() /** páros */ && $date->format('W') % 2 === 1 /** páratlan */)
        {
            return false;
        }

        return true;
    }
}
