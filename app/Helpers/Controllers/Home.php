<?php
declare(strict_types=1);

namespace App\Helpers\Controllers;

use App\Models\Reception;
use App\Models\ReceptionRepetitive_repetitivitydays;
use App\Models\Reservation;
use Illuminate\Support\Collection;
use App\Models\ReceptionRepetitive_repetitivityKinds;

/**
 * home
 */
final class Home
{
    readonly private Reception $reception;
    readonly private Reservation $reservation;

    /**
     * constructor
     *
     * @param $reception   - reception
     * @param $reservation - reservation
     */
    public function __construct(Reception $reception, Reservation $reservation)
    {
        $this->reception = $reception;
        $this->reservation = $reservation;
    }

    /**
     * get and format reception times
     */
    public function getAndFormatReceptionTimes() : Collection
    {
        $receptionTimes = $this->reception->getAllWithRepetitiveInfo();
        $this->formatRepetitivity($receptionTimes);
        $this->formatRepetitivityDays($receptionTimes);
        $this->formatDates($receptionTimes, 'start');
        $this->formatDates($receptionTimes, 'end');
        foreach($receptionTimes as $item)
        {
            $item->until = (string)$item->until;
            if ($item->until !== '')
            {
                try
                {
                    $item->until = (new \DateTime($item->until))->format('Y-M-d');
                }
                catch(\Exception)
                {
                    $item->until = '?';
                }
            }
        }
        return $receptionTimes;
    }

    /**
     * get and format reservation times
     */
    public function getAndFormatReservationTimes() : Collection
    {
        $reservationTimes = $this->reservation->getAll();
        $this->formatDates($reservationTimes, 'start');
        $this->formatDates($reservationTimes, 'end');
        return $reservationTimes;
    }

    /**
     * format repetitivity
     *
     * @param $dates - dates
     */
    private function formatRepetitivity(Collection $dates) : void
    {
        foreach($dates as $item)
        {
            if ($item->repetitivity === null)
            {
                $item->repetitivity = 'egyszeri';
            }
            else
            {
                $item->repetitivity = ReceptionRepetitive_repetitivityKinds::from($item->repetitivity);
                if ($item->repetitivity === null)
                {
                    $item->repetitivity = '?';
                }
                else
                {
                    $item->repetitivity = $item->repetitivity->getAsString();
                }
            }
        }
    }

    /**
     * format repetitivity
     *
     * @param $dates - dates
     */
    private function formatRepetitivityDays(Collection $dates) : void
    {
        foreach($dates as $item)
        {
            if ($item->repetitivityday === null)
            {
                $item->repetitivityday = '';
            }
            else
            {
                $item->repetitivityday = ReceptionRepetitive_repetitivitydays::from((int)$item->repetitivityday);
                if ($item->repetitivityday === null)
                {
                    $item->repetitivityday = '?';
                }
                else
                {
                    $item->repetitivityday = $item->repetitivityday->getAsString();
                }
            }
        }
    }

    /**
     * format dates
     *
     * @param $dates - dates
     * @param $dateKeyFieldName - date key fieldname
     */
    private function formatDates(Collection $dates, string $dateKeyFieldName): void
    {
        foreach($dates as $item)
        {
            try
            {
                $item->$dateKeyFieldName = (new \DateTime($item->$dateKeyFieldName))->format('Y-M-d H:i:s');
            }
            catch(\Exception)
            {
                $item->$dateKeyFieldName = '(hibás dátum: '.$item->$dateKeyFieldName.')';
            }
        }
    }
}
