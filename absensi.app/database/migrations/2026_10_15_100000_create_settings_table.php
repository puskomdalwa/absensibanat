<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique()->index();
                $table->longText('value')->nullable();
                $table->string('type')->default('string');
                $table->string('group')->default('general')->index();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Default setting for nominal display mode
        // Options: 'show' (tampilkan semua), 'hide' (sembunyikan semua), 'admin_only' (hanya admin/superadmin)
        if (! DB::table('settings')->where('key', 'nominal_display_mode')->exists()) {
            DB::table('settings')->insert([
                'key' => 'nominal_display_mode',
                'value' => 'show',
                'type' => 'string',
                'group' => 'presensi',
                'description' => 'Mode visibilitas nominal honorarium presensi (show: tampilkan semua, hide: sembunyikan semua, admin_only: hanya admin & superadmin)',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
