<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ReceptionRepetitive_repetitivitydays;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ReceptionRepetitive_repetitivityKinds;

class ReceptionSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * php artisan make:model Reception
         * php artisan make:model ReceptionRepetitive
         * php artisan make:model Reservation
         *
         * php artisan config:clear
         * php artisan migrate
         * php artisan db:seed --class ReceptionSeed
         */

        // 2024-09-08-án 8-10 óra
        DB::table('receptions')->insert([
            'start' => '2024-09-08 08:00:00',
            'end'   => '2024-09-08 10:00:00'
        ]);

        // 2024-01-01-től minden páros héten hétfőn 10-12 óra
        $lastId = DB::table('receptions')->insertGetId([
            'start' => '2024-01-01 10:00:00',
            'end'   => '2024-01-01 12:00:00'
        ]);
        DB::table('receptions_repetitives')->insert([
            'reception_id'    => $lastId,
            'repetitivity'    => ReceptionRepetitive_repetitivityKinds::EVEN->value,
            'repetitivityday' => ReceptionRepetitive_repetitivitydays::MONDAY
        ]);

        // 2024-01-01-től minden páratlan héten szerda 12-16 óra
        $lastId = DB::table('receptions')->insertGetId([
            'start' => '2024-01-01 12:00:00',
            'end'   => '2024-01-01 16:00:00'
        ]);
        DB::table('receptions_repetitives')->insert([
            'reception_id'    => $lastId,
            'repetitivity'    => ReceptionRepetitive_repetitivityKinds::ODD->value,
            'repetitivityday' => ReceptionRepetitive_repetitivitydays::WEDNESDAY
        ]);

        // 2024-01-01-től minden héten pénteken 10-16 óra
        $lastId = DB::table('receptions')->insertGetId([
            'start' => '2024-01-01 10:00:00',
            'end'   => '2024-01-01 16:00:00'
        ]);
        DB::table('receptions_repetitives')->insert([
            'reception_id'    => $lastId,
            'repetitivity'    => ReceptionRepetitive_repetitivityKinds::ALL->value,
            'repetitivityday' => ReceptionRepetitive_repetitivitydays::FRIDAY
        ]);

        // 2024-06-01-től 2024-11-30-ig minden héten csütörtökön 16-20 óra
        $lastId = DB::table('receptions')->insertGetId([
            'start' => '2024-06-01 16:00:00',
            'end'   => '2024-06-01 20:00:00'
        ]);
        DB::table('receptions_repetitives')->insert([
            'reception_id'    => $lastId,
            'repetitivity'    => ReceptionRepetitive_repetitivityKinds::ALL->value,
            'repetitivityday' => ReceptionRepetitive_repetitivitydays::THURSDAY,
            'until'           => '2024-11-30'
        ]);
    }
}
