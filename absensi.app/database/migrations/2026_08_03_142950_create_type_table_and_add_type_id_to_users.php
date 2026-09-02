<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTypeTableAndAddTypeIdToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('type_id')
                ->nullable()
                ->after('departemen_id')
                ->constrained('type')
                ->nullOnDelete();
        });

        // Insert default type 'santri'
        DB::table('type')->insert([
            'nama' => 'santri',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert 'santri' category into kategori table
        DB::table('kategori')->updateOrInsert(
            ['kode' => 'SANTRI'],
            [
                'nama' => 'santri',
                'selisih' => 0,
                'nominal' => 10000,
                'keterangan' => 'Kategori khusus untuk type user santri.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('type_id');
        });

        Schema::dropIfExists('type');

        DB::table('kategori')->where('kode', 'SANTRI')->delete();
    }
}
