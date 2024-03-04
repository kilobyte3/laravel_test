<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ReceptionRepetitive_repetitivitydays;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receptions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->dateTime('start');
            $table->dateTime('end');
        });
        Schema::create('receptions_repetitives', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger('reception_id')->primary();
            $table->tinyInteger('repetitivity', false, true); // App\Models\ReceptionRepetitive_repetitivitydays::REPETITIVITY_KINDS
            $table->enum('repetitivityday', ReceptionRepetitive_repetitivitydays::getKeys());
            $table->date('until')->nullable();
            $table->foreign('reception_id')->on('receptions')->references('id')->onDelete('cascade');
        });
        Schema::create('reservations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('clientname', 255);
            $table->dateTime('start');
            $table->dateTime('end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receptions');
    }
};
