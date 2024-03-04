<?php
declare(strict_types=1);

namespace App\Helpers\Controllers;

use App\Library\DateTimeRanges;

/**
 * calendar
 */
final class Calendar
{
    /**
     * convert start-end to range
     *
     * @param $source - source
     */
    public function convertStartEndToRange(array $source) : array
    {
        foreach($source as &$item)
        {
            $item['range'] = new DateTimeRanges($item['start'], $item['end']);
            unset($item['start'], $item['end']);
        }
        unset($item);
        return $source;
    }

    /**
     * get max date
     *
     * @param $source - source
     */
    public function getMaxDate(array $source) : \DateTimeImmutable
    {
        $result = [];
        foreach($source as $date)
        {
            $result[] = $date;
        }
        rsort($result);
        return $result[0];
    }

    /**
     * get answer for request
     *
     * @param $overlapCheckingResult   - overlapchecking result
     * @param $reservationDatesRecords - reservation dates records
     * @param $receptionDatesRecords   - reception dates records
     */
    public function getAnswerForRequest(array $overlapCheckingResult, array $reservationDatesRecords, array $receptionDatesRecords) : array
    {
        $result = [];
        if ($overlapCheckingResult['result']->isOk())
        {
            $result[] = 'ok';
        }
        else
        {
            if ($overlapCheckingResult['result']->isNotInWhitelist())
            {
                $result[] = 'nwl';
            }
            if ($overlapCheckingResult['result']->isPartlyInWhitelist())
            {
                $result[] = 'pwl';
                $result[] = $receptionDatesRecords[$overlapCheckingResult['index']]['range']->getStart()->format('Y-M-d H:i:s');
                $result[] = $receptionDatesRecords[$overlapCheckingResult['index']]['range']->getEnd()->format('Y-M-d H:i:s');
            }
            if ($overlapCheckingResult['result']->isInBlacklist())
            {
                $result[] = 'bl';
                $result[] = $reservationDatesRecords[$overlapCheckingResult['index']]['range']->getStart()->format('Y-M-d H:i:s');
                $result[] = $reservationDatesRecords[$overlapCheckingResult['index']]['range']->getEnd()->format('Y-M-d H:i:s');
                $result[] = $reservationDatesRecords[$overlapCheckingResult['index']]['clientname'];
            }
        }
        if (sizeof($result) === 0)
        {
            throw new \RuntimeException('Valamilyen ellenörzés kimaradt!');
        }
        return $result;
    }
}
