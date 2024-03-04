<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * reception repetitive
 */
class ReceptionRepetitive extends Model
{
    protected $table = 'reception_repetitives';
    protected $primaryKey = 'reception_id';
}
