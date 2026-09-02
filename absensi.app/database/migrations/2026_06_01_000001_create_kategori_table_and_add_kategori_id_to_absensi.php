<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateKategoriTableAndAddKategoriIdToAbsensi extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('kategori')) {
            Schema::create('kategori', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('kode')->unique();
                $table->time('waktu_mulai');
                $table->time('waktu_akhir');
                $table->decimal('nominal', 15, 2)->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('absensi') && !Schema::hasColumn('absensi', 'kategori_id')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->foreignId('kategori_id')
                    ->nullable()
                    ->after('device_id')
                    ->constrained('kategori')
                    ->nullOnDelete();
            });
        }

        DB::table('kategori')->updateOrInsert(
            ['kode' => 'UMUM-13-19'],
            [
                'nama' => 'Umum 13:00 - 19:00',
                'waktu_mulai' => '13:00:00',
                'waktu_akhir' => '19:00:00',
                'nominal' => 0,
                'keterangan' => 'Kategori default untuk kalkulasi absensi rentang 13:00 sampai 19:00.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $kategoriId = DB::table('kategori')->where('kode', 'UMUM-13-19')->value('id');
        if ($kategoriId && Schema::hasTable('absensi') && Schema::hasColumn('absensi', 'kategori_id')) {
            DB::table('absensi')->whereNull('kategori_id')->update(['kategori_id' => $kategoriId]);
        }
    }

    public function down()
    {
        if (Schema::hasTable('absensi') && Schema::hasColumn('absensi', 'kategori_id')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->dropConstrainedForeignId('kategori_id');
            });
        }

        Schema::dropIfExists('kategori');
    }
}
