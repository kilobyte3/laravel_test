<?php
declare(strict_types=1);

namespace App\Library;

/**
 * OverlappingDateCheckerResult
 */
enum OverlappingDateCheckerResult
{
    case OK;
    case NOTINWHITELIST;
    case PARTLYINWHITELIST;
    case INBLACKLIST;

    /**
     * is ok?
     */
    public function isOk() : bool
    {
        return $this === self::OK;
    }

    /**
     * is not in whitelist?
     */
    public function isNotInWhitelist() : bool
    {
        return $this === self::NOTINWHITELIST;
    }

    /**
     * is partly in whitelist?
     */
    public function isPartlyInWhitelist() : bool
    {
        return $this === self::PARTLYINWHITELIST;
    }

    /**
     * is in blacklist?
     */
    public function isInBlacklist() : bool
    {
        return $this === self::INBLACKLIST;
    }
}
