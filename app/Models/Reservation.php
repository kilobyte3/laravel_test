<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * reservation
 */
class Reservation extends Model
{
    protected $table = 'reservations';
    protected $primaryKey = 'flight_id';

    /**
     * get all
     */
    public function getAll() : Collection
    {
        return DB::table($this->table)->orderBy('start')->get(['id', 'clientname', 'start', 'end']);
    }

    /**
     * get for checking
     */
    public function getForChecking() : Collection
    {
        return DB::table($this->table)->get(['clientname', 'start', 'end']);
    }

    /**
     * convert to independent format
     *
     * @param $records - records
     */
    public function convertToIndependentFormat(Collection $records) : array
    {
        $result = [];
        foreach($records as $item)
        {
            $item = (array)$item;
            if (isset($item['id']))
            {
                $item['id'] = (int)$item['id'];
            }
            if (isset($item['start']))
            {
                $item['start'] = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['start']);
            }
            if (isset($item['end']))
            {
                $item['end'] = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['end']);
            }
            $result[] = $item;
        }
        return $result;
    }
}
