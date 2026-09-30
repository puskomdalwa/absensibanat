<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFingerspotTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('fingerspot_commands')) {
            Schema::create('fingerspot_commands', function (Blueprint $table) {
                $table->id();
                $table->string('trans_id')->nullable()->index();
                $table->string('cloud_id')->nullable()->index();
                $table->string('device_name')->nullable();
                $table->string('command_type')->index();
                $table->json('payload_request')->nullable();
                $table->json('payload_response')->nullable();
                $table->json('callback_payload')->nullable();
                $table->string('status')->default('pending')->index(); // pending, success, failed
                $table->string('status_code')->nullable(); // '1', '2'
                $table->text('message')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('fingerspot_device_users')) {
            Schema::create('fingerspot_device_users', function (Blueprint $table) {
                $table->id();
                $table->string('cloud_id')->index();
                $table->string('pin')->index();
                $table->string('name')->nullable();
                $table->unsignedTinyInteger('privilege')->default(1); // 1=user, 2=admin, 3=subadmin
                $table->unsignedInteger('finger')->default(0);
                $table->unsignedInteger('face')->default(0);
                $table->unsignedInteger('vein')->default(0);
                $table->string('password')->nullable();
                $table->string('rfid')->nullable();
                $table->longText('template')->nullable();
                $table->timestamp('last_sync_at')->nullable();
                $table->timestamps();

                $table->unique(['cloud_id', 'pin']);
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
        Schema::dropIfExists('fingerspot_device_users');
        Schema::dropIfExists('fingerspot_commands');
    }
}
