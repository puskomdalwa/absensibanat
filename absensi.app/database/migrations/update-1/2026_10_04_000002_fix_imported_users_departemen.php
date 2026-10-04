<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixImportedUsersDepartemen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Pastikan departemen Admin tersedia
        $adminDept = DB::table('departemen')->whereRaw('LOWER(TRIM(nama)) = ?', ['admin'])->first();
        if (!$adminDept) {
            $adminKode = DB::table('departemen')->where('kode', '001')->exists() ? null : '001';
            if (!$adminKode) {
                $maxNum = DB::table('departemen')->whereRaw('kode REGEXP "^[0-9]+$"')->max(DB::raw('CAST(kode AS UNSIGNED)'));
                $adminKode = sprintf('%03d', ($maxNum ?: 0) + 1);
            }
            $adminDeptId = DB::table('departemen')->insertGetId([
                'kode'       => $adminKode,
                'nama'       => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $adminDeptId = $adminDept->id;
        }

        // 2. Pastikan departemen Dosen tersedia
        $dosenDept = DB::table('departemen')->whereRaw('LOWER(TRIM(nama)) = ?', ['dosen'])->first();
        if (!$dosenDept) {
            $dosenKode = DB::table('departemen')->where('kode', '002')->exists() ? null : '002';
            if (!$dosenKode) {
                $maxNum = DB::table('departemen')->whereRaw('kode REGEXP "^[0-9]+$"')->max(DB::raw('CAST(kode AS UNSIGNED)'));
                $dosenKode = sprintf('%03d', ($maxNum ?: 1) + 1);
            }
            $dosenDeptId = DB::table('departemen')->insertGetId([
                'kode'       => $dosenKode,
                'nama'       => 'Dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $dosenDeptId = $dosenDept->id;
        }

        // 3. Perbaiki departemen untuk akun Admin dari template import
        $adminKodes = ['9818', '80110'];
        DB::table('users')
            ->where(function ($q) use ($adminKodes) {
                $q->whereIn('kode', $adminKodes)
                  ->orWhereIn('username', $adminKodes);
            })
            ->update([
                'departemen_id' => $adminDeptId,
                'updated_at'    => now(),
            ]);

        // 4. Perbaiki departemen untuk 55 civitas Dosen dari template import
        $dosenKodes = [
            '80117', '80290', '2113069402', '80162', '260102', '80235', '80258', '260101', '80257', '80147',
            '80228', '24110401', '80150', '80246', '2121118001', '260106', '80237', '80222', '80149', '1122334502',
            '80239', '80233', '80262', '80240', '80201', '24110403', '80122', '80277', '1122334453', '24110405',
            '80164', '24110404', '24110402', '80231', '80186', '80260', '80245', '80181', '80050', '260107',
            '2027068602', '80206', '80223', '260104', '82101', '80183', '260103', '80123', '260108', '80254',
            '23456797', '260105', '80152', '80221', '80178'
        ];

        DB::table('users')
            ->where(function ($q) use ($dosenKodes) {
                $q->whereIn('kode', $dosenKodes)
                  ->orWhereIn('username', $dosenKodes);
            })
            ->update([
                'departemen_id' => $dosenDeptId,
                'updated_at'    => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Tidak perlu revert karena mengembalikan ke nilai benar
    }
}
