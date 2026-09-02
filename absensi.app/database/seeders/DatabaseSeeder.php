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
        DB::table('departemen')->insert([
            'kode' => '001',
            'nama' => 'Admin',
        ]);
        DB::table('role')->insert([
            'akses' => 'admin',
        ]);
        DB::table('role')->insert([
            'akses' => 'user',
        ]);
        DB::table('role')->insert([
            'akses' => 'staff',
        ]);
        DB::table('users')->insert([
            'email' => 'admin@example.com',
            'username' => 'admin',
            'name' => 'Admin',
            'password' => bcrypt('admin'),
            'role_id' => 1,
            'departemen_id' => 1,
            'jenis_kelamin' => '*',
        ]);
        DB::table('verify')->insert([
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

        DB::table('device')->insert([
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
