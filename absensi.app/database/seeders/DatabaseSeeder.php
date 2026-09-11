<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(10)->create();
        DB::table('departemen')->insertOrIgnore([
            'kode' => '001',
            'nama' => 'Admin',
        ]);
        DB::table('role')->insertOrIgnore([
            'akses' => 'admin',
        ]);
        DB::table('role')->insertOrIgnore([
            'akses' => 'user',
        ]);
        DB::table('role')->insertOrIgnore([
            'akses' => 'staff',
        ]);
        $adminRoleId = DB::table('role')->where('akses', 'admin')->value('id') ?? 1;
        $adminDeptId = DB::table('departemen')->value('id') ?? 1;

        DB::table('users')->insertOrIgnore([
            'email' => 'admin@example.com',
            'username' => 'admin',
            'name' => 'Admin',
            'password' => bcrypt('admin'),
            'role_id' => $adminRoleId,
            'departemen_id' => $adminDeptId,
            'jenis_kelamin' => '*',
        ]);
        DB::table('verify')->insertOrIgnore([
            [
                'name' => 'finger',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'password',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'card',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'face',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'vein',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'QR',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('device')->insertOrIgnore([
            [
                'name' => 'ABSENSI LOBBY',
                'cloud_id' => 'C2642CA867122A34',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ABSENSI LT1',
                'cloud_id' => 'C2642CA867330F2C',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
