<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDurationCategoriesAndRecalculateAbsensi extends Migration
{
    public function up()
    {
        // Hapus kolom waktu_mulai dan waktu_akhir dari tabel kategori
        if (Schema::hasColumn('kategori', 'waktu_mulai')) {
            Schema::table('kategori', function (Blueprint $table) {
                $table->dropColumn('waktu_mulai');
            });
        }

        if (Schema::hasColumn('kategori', 'waktu_akhir')) {
            Schema::table('kategori', function (Blueprint $table) {
                $table->dropColumn('waktu_akhir');
            });
        }

        // Tambah kolom selisih di tabel kategori
        if (!Schema::hasColumn('kategori', 'selisih')) {
            Schema::table('kategori', function (Blueprint $table) {
                $table->integer('selisih')->default(0)->after('kode')->comment('Selisih minimal (menit)');
            });
        }

        // Tambah kolom selisih di tabel absensi (jika diperlukan untuk menyimpan histori)
        if (!Schema::hasColumn('absensi', 'selisih')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->integer('selisih')->nullable()->after('sore')->comment('Selisih waktu dalam menit');
            });
        }

        $now = now();
        $categories = [
            [
                'kode' => 'KAT-A',
                'nama' => 'Kategori A (5 Jam)',
                'selisih' => 300,
                'nominal' => 25000,
                'keterangan' => 'Selisih absensi 5 jam (300 menit) atau lebih.',
            ],
            [
                'kode' => 'KAT-B',
                'nama' => 'Kategori B (4 Jam)',
                'selisih' => 180,
                'nominal' => 20000,
                'keterangan' => 'Selisih absensi 3 jam (180 menit) sampai sebelum 5 jam.',
            ],
            [
                'kode' => 'KAT-C',
                'nama' => 'Kategori C (3 Jam)',
                'selisih' => 0, // Jadikan 0 agar menjadi default paling rendah, atau 180 jika mutlak harus 3 jam. Lebih aman 0 untuk "kurang dari 4 jam".
                'nominal' => 15000,
                'keterangan' => 'Selisih absensi kurang dari 4 jam.',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('kategori')->updateOrInsert(
                ['kode' => $category['kode']],
                array_merge($category, [
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
        }

        $kategoriIds = DB::table('kategori')
            ->whereIn('kode', ['KAT-A', 'KAT-B', 'KAT-C'])
            ->pluck('id', 'kode');

        // Jadikan KAT-C sebagai default untuk semua absensi
        if (isset($kategoriIds['KAT-C'])) {
            DB::table('absensi')->update(['kategori_id' => $kategoriIds['KAT-C']]);
        }

        // Hapus kategori UMUM lama
        DB::table('kategori')->where('kode', 'UMUM-13-19')->delete();

        // Recalculate selisih dan kategori untuk data yang sudah ada
        DB::table('absensi')
            ->whereNotNull('pagi')
            ->whereNotNull('sore')
            ->orderBy('id')
            ->chunkById(200, function ($items) use ($kategoriIds) {
                foreach ($items as $item) {
                    $minutes = $this->selisihMenit($item->tgl_absen, $item->pagi, $item->sore);
                    $code = 'KAT-C';

                    if ($minutes >= 300) {
                        $code = 'KAT-A';
                    } elseif ($minutes >= 180) {
                        $code = 'KAT-B';
                    }

                    $update = ['selisih' => $minutes];

                    if (isset($kategoriIds[$code])) {
                        $update['kategori_id'] = $kategoriIds[$code];
                    }

                    DB::table('absensi')
                        ->where('id', $item->id)
                        ->update($update);
                }
            });
    }

    public function down()
    {
        DB::table('absensi')
            ->whereIn('kategori_id', function ($query) {
                $query->select('id')
                    ->from('kategori')
                    ->whereIn('kode', ['KAT-A', 'KAT-B', 'KAT-C']);
            })
            ->update(['kategori_id' => null]);

        DB::table('kategori')->whereIn('kode', ['KAT-A', 'KAT-B', 'KAT-C'])->delete();

        // Hapus kolom selisih dari tabel absensi
        if (Schema::hasColumn('absensi', 'selisih')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->dropColumn('selisih');
            });
        }

        // Hapus kolom selisih dari tabel kategori
        if (Schema::hasColumn('kategori', 'selisih')) {
            Schema::table('kategori', function (Blueprint $table) {
                $table->dropColumn('selisih');
            });
        }

        // Kembalikan kolom waktu_mulai dan waktu_akhir
        if (!Schema::hasColumn('kategori', 'waktu_mulai')) {
            Schema::table('kategori', function (Blueprint $table) {
                $table->time('waktu_mulai')->nullable()->after('nominal');
            });
        }

        if (!Schema::hasColumn('kategori', 'waktu_akhir')) {
            Schema::table('kategori', function (Blueprint $table) {
                $table->time('waktu_akhir')->nullable()->after('waktu_mulai');
            });
        }

        // Kembalikan UMUM-13-19
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
        if ($kategoriId) {
            DB::table('absensi')->whereNull('kategori_id')->update(['kategori_id' => $kategoriId]);
        }
    }

    private function selisihMenit($date, $start, $end): int
    {
        if (!$date || !$start || !$end || $start === '-' || $end === '-') {
            return 0;
        }

        $startedAt = Carbon::parse($date . ' ' . $start);
        $endedAt = Carbon::parse($date . ' ' . $end);

        if ($endedAt->lt($startedAt)) {
            return 0;
        }

        return $startedAt->diffInMinutes($endedAt);
    }
}
