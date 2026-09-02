<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EnsureStaffRoleExists extends Migration
{
    /**
     * Pastikan role staff tersedia juga pada database yang sudah berjalan.
     *
     * @return void
     */
    public function up()
    {
        if (! DB::table('role')->where('akses', 'staff')->exists()) {
            DB::table('role')->insert([
                'akses' => 'staff',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Role tidak dihapus saat rollback agar akun staff yang sudah dibuat
     * tidak kehilangan referensi foreign key.
     *
     * @return void
     */
    public function down()
    {
        // Data role dipertahankan dengan sengaja.
    }
}
