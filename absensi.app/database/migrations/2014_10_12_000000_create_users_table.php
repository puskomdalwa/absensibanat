<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->nullable();
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('name');
            $table->string('password');
            $table->foreignId('role_id')->constrained('role');
            $table->foreignId('departemen_id')->nullable()->constrained('departemen');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan', '*'])->nullable();
            $table->string('photo')->nullable();
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
        Schema::dropIfExists('users');
    }
}
