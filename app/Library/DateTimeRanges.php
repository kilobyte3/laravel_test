<?php
declare(strict_types=1);

namespace App\Library;

/**
 * date-time ranges
 */
final class DateTimeRanges
{
    readonly private \DateTimeImmutable $start, $end;

    /**
     * constructor
     *
     * @param $start - start
     * @param $end   - end
     */
    public function __construct(\DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * get start
     */
    public function getStart() : \DateTimeImmutable
    {
        return $this->start;
    }

    /**
     * get end
     */
    public function getEnd() : \DateTimeImmutable
    {
        return $this->end;
    }

    /**
     * is partly overlapped with another range?
     *
     * @param $range - range obj
     */
    public function isPartlyOverlappedWith(self $range) : bool
    {
        return $this->getStart() < $range->getEnd() && $this->getEnd() > $range->getStart();
    }

    /**
     * is fully overlapped with another range?
     *
     * @param $range - range obj
     */
    public function isFullyOverlappedWith(self $range) : bool
    {
        return $this->getStart() <= $range->getStart() && $this->getEnd() >= $range->getEnd();
    }
}
