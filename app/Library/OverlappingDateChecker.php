<?php
declare(strict_types=1);

namespace App\Library;

/**
 * overlapping date checker
 */
final class OverlappingDateChecker
{
    /** @var DateTimeRanges[] */
    readonly private array $whiteListDates, $blackListDates;

    /**
     * constructor
     *
     * @param DateTimeRanges[] $whiteListDates - white list dates
     * @param DateTimeRanges[] $blackListDates - black list dates
     */
    public function __construct(array $whiteListDates, array $blackListDates)
    {
        $this->whiteListDates = $whiteListDates;
        $this->blackListDates = $blackListDates;
    }

    /**
     * do check
     *
     * @param $thisRange - this range
     */
    public function doCheck(DateTimeRanges $thisRange) : array
    {
        $result = $this->checkWhitelist($thisRange);
        if (is_array($result))
        {
            return $result;
        }

        $result = $this->checkBlacklist($thisRange);
        if (is_array($result))
        {
            return $result;
        }

        return [
            'result' => OverlappingDateCheckerResult::OK
        ];
    }

    /**
     * check whitelist (MUST fully overlap!)
     *
     * @param $thisRange - this range
     */
    private function checkWhitelist(DateTimeRanges $thisRange) : array | bool
    {
        $inWhiteList = false;
        foreach($this->whiteListDates as $i => $range)
        {
            $isFullyOverlapped = $range->isFullyOverlappedWith($thisRange);
            $isPartiallyOverlapped = $range->isPartlyOverlappedWith($thisRange);
            if ($isFullyOverlapped && $isPartiallyOverlapped)
            {
                $inWhiteList = true;
                break;
            }
            if (!$isFullyOverlapped && $isPartiallyOverlapped)
            {
                return [
                    'result' => OverlappingDateCheckerResult::PARTLYINWHITELIST,
                    'index'  => $i
                ];
            }
        }
        if (!$inWhiteList)
        {
            return [
                'result' => OverlappingDateCheckerResult::NOTINWHITELIST
            ];
        }

        return true;
    }

    /**
     * check blacklist (must NOT overlap!)
     *
     * @param $thisRange - this range
     */
    private function checkBlacklist(DateTimeRanges $thisRange) : array | bool
    {
        foreach($this->blackListDates as $i => $range)
        {
            if ($range->isPartlyOverlappedWith($thisRange))
            {
                return [
                    'result' => OverlappingDateCheckerResult::INBLACKLIST,
                    'index'  => $i
                ];
            }
        }
        return true;
    }
}
