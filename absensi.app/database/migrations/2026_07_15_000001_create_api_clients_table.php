<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('api_clients')) {
            Schema::create('api_clients', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('api_key', 64)->unique();
                $table->string('secret_key', 128);
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_clients');
    }
}
