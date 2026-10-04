<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EnsureSuperadminRoleExists extends Migration
{
    /**
     * Pastikan role superadmin tersedia juga pada database yang sudah berjalan.
     *
     * @return void
     */
    public function up()
    {
        if (! DB::table('role')->where('akses', 'superadmin')->exists()) {
            DB::table('role')->insert([
                'akses' => 'superadmin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Role tidak dihapus saat rollback agar akun superadmin yang sudah dibuat
     * tidak kehilangan referensi foreign key.
     *
     * @return void
     */
    public function down()
    {
        // Data role dipertahankan dengan sengaja.
    }
}
