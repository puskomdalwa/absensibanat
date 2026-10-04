<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateSuperadminRoleAndUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Pastikan role superadmin tersedia di tabel role
        if (! DB::table('role')->where('akses', 'superadmin')->exists()) {
            DB::table('role')->insert([
                'akses' => 'superadmin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $superadminRoleId = DB::table('role')->where('akses', 'superadmin')->value('id');
        $adminDeptId = DB::table('departemen')->where('nama', 'Admin')->value('id') 
            ?? DB::table('departemen')->value('id');

        // 2. Buat akun user default untuk superadmin jika belum ada
        if (! DB::table('users')->where('username', 'superadmin')->exists()) {
            DB::table('users')->insert([
                'name'          => 'Superadmin',
                'username'      => 'superadmin',
                'email'         => 'superadmin@dalwa.ac.id',
                'password'      => Hash::make('superadmin'),
                'role_id'       => $superadminRoleId,
                'departemen_id' => $adminDeptId,
                'jenis_kelamin' => '*',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        } else {
            // Jika user superadmin sudah ada, pastikan role-nya adalah superadmin
            DB::table('users')->where('username', 'superadmin')->update([
                'role_id'    => $superadminRoleId,
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
        // Hapus akun user superadmin saat rollback
        DB::table('users')->where('username', 'superadmin')->delete();

        // Data role dipertahankan agar tidak merusak referensi jika ada relasi lain
    }
}
