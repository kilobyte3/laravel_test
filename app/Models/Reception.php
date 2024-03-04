<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * reception
 */
class Reception extends Model
{
    protected $table = 'receptions';
    protected $primaryKey = 'id';

    /**
     * get all with repetitive info
     */
    public function getAllWithRepetitiveInfo() : Collection
    {
        return DB::table($this->table)->leftJoin('receptions_repetitives', 'receptions_repetitives.reception_id', '=', 'receptions.id')->orderBy('start')->get(['start','end','repetitivity','repetitivityday','until']);
    }

    /**
     * get for checking
     */
    public function getForChecking() : Collection
    {
        return DB::table($this->table)->leftJoin('receptions_repetitives', 'receptions_repetitives.reception_id', '=', 'receptions.id')->get(['start','end','repetitivity','repetitivityday','until']);
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
            if (isset($item['repetitivityday']))
            {
                $item['repetitivityday'] = ReceptionRepetitive_repetitivitydays::from((int)$item['repetitivityday']);
            }
            if ($item['repetitivity'] !== null)
            {
                $item['repetitivity'] = ReceptionRepetitive_repetitivityKinds::from((int)$item['repetitivity']);
            }
            if (is_string($item['until']))
            {
                $item['until'] = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['until'].' 23:59:59');
            }
            $result[] = $item;
        }
        return $result;
    }
}
