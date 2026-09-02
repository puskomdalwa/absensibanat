<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->constrained('users');
            $table->date('tgl_absen');
            $table->time('pagi')->nullable();
            $table->time('sore')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->foreignId('verify_id')->constrained('verify');
            $table->foreignId('device_id')->constrained('device');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absensi');
    }
}
