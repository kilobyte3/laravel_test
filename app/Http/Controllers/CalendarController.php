<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Controllers\Calendar;
use App\Library\DateTimeRanges;
use App\Library\OverlappingDateChecker;
use App\Library\RepetitiveDateToConcreteDate;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Reception;
use App\Helpers\Controllers\Home;
use Illuminate\Support\Facades\DB;

/**
 * calendar controller
 */
class CalendarController extends Controller
{
    readonly private Reservation $reservation;
    readonly private Reception $reception;

    /**
     * constructor
     *
     * @param $reservation - reservation
     * @param $reception   - reception
     */
    public function __construct(Reservation $reservation, Reception $reception)
    {
        $this->reservation = $reservation;
        $this->reception = $reception;
    }

    /**
     * delete reservation
     *
     * @param $request - request
     */
    public function deletereservation(Request $request)
    {
        $answer = '';
        if ($request->has('id'))
        {
            if (DB::table($this->reservation->getTable())->delete((int)$request->get('id')) === 1)
            {
                $answer = 'Törlés végrehajtva!';
            }
            else
            {
                $answer = 'A törlés nem sikerült, töltse újra az oldalt!';
            }
        }
        return response([$answer]);
    }

    /**
     * get reservations
     *
     * @param $request - request
     */
    public function getreservations(Request $request)
    {
        $answer = app(Home::class)->getAndFormatReservationTimes();
        foreach($answer as $item)
        {
            $item->clientname = htmlspecialchars($item->clientname);
        }
        return response($answer);
    }

    /**
     * get reception
     *
     * @param $request - request
     */
    public function getreceptions(Request $request)
    {
        return response(app(Home::class)->getAndFormatReceptionTimes());
    }

    /**
     * add reservations
     *
     * @param $request - request
     */
    public function addreservation(Request $request)
    {
        try
        {
            $start = new \DateTimeImmutable($request->get('start'));
        }
        catch(\Exception)
        {
            $start = null;
        }
        try
        {
            $end = new \DateTimeImmutable($request->get('end'));
        }
        catch(\Exception)
        {
            $end = null;
        }

        if (is_object($start) && is_object($end) && $request->has('clientname'))
        {
            $reservationDatesRecord = $this->reservation->getForChecking();
            $reservationDatesRecord = $this->reservation->convertToIndependentFormat($reservationDatesRecord);
            $reservationDatesRecord = app(Calendar::class)->convertStartEndToRange($reservationDatesRecord);

            $receptionDatesRecords = $this->reception->getForChecking();
            $receptionDatesRecords = $this->reception->convertToIndependentFormat($receptionDatesRecords);
            $receptionDatesRecords = app(Calendar::class)->convertStartEndToRange($receptionDatesRecords);

            $answer = app(Calendar::class)->getAnswerForRequest(
                app()->makeWith(OverlappingDateChecker::class, [
                    'whiteListDates' => app()->make(RepetitiveDateToConcreteDate::class)->doIt($receptionDatesRecords, app(Calendar::class)->getMaxDate([$end]+$receptionDatesRecords)),
                    'blackListDates' => array_map(fn(array $item) => $item['range'], $reservationDatesRecord)
                ])->doCheck(new DateTimeRanges($start, $end)),
                $reservationDatesRecord,
                $receptionDatesRecords
            );
            if ($answer[0] === 'ok')
            {
                DB::table($this->reservation->getTable())->insert([
                    'clientname' => trim((string)$request->get('clientname')),
                    'start'      => $start->format('Y-m-d H:i:s'),
                    'end'        => $end->format('Y-m-d H:i:s')
                ]);
            }

        }
        else
        {
            $answer = ['Hibás dátumok!'];
        }
        return response($answer);
    }

    /**
     * get calendar
     *
     * @param $request - request
     */
    public function getcalendar(Request $request)
    {
        return response([
            'receptionTimesPure' => $this->reception->getAllWithRepetitiveInfo()->toArray(),
            'reservedTimesPure'  => $this->reservation->getAll()->toArray()
        ]);
    }
}
