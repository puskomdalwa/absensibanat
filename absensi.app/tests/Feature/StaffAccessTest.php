<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffAccessTest extends TestCase
{
    use DatabaseTransactions;

    private function makeUser(string $role, string $prefix): User
    {
        $roleId = Role::where('akses', $role)->value('id');
        $departemenId = Departemen::query()->value('id');
        $id = random_int(800000000, 899999999);

        return User::create([
            'id' => $id,
            'username' => $prefix . $id,
            'name' => ucfirst($prefix) . ' Test',
            'email' => $prefix . $id . '@example.test',
            'password' => Hash::make('password'),
            'role_id' => $roleId,
            'departemen_id' => $departemenId,
            'jenis_kelamin' => 'Laki-laki',
        ]);
    }

    public function test_staff_can_view_admin_attendance_and_users(): void
    {
        $staff = $this->makeUser('staff', 'staff');
        $employee = $this->makeUser('user', 'employee');

        DB::table('absensi')->insert([
            'users_id' => $employee->id,
            'tgl_absen' => now()->toDateString(),
            'pagi' => '08:00:00',
            'sore' => '16:00:00',
            'verify_id' => DB::table('verify')->value('id'),
            'device_id' => DB::table('device')->value('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($staff)
            ->get(route('admin.absensi.index'))
            ->assertOk();

        $this->actingAs($staff)
            ->get(route('admin.absensi.data', [
                'role_id' => '*',
                'departemen_id' => '*',
                'filter_type' => 'semua',
            ]))
            ->assertOk()
            ->assertSee($employee->name);

        $this->actingAs($staff)
            ->get(route('admin.user.index'))
            ->assertOk();
    }

    public function test_staff_created_accounts_are_always_regular_users(): void
    {
        $staff = $this->makeUser('staff', 'staff');
        $adminRoleId = Role::where('akses', 'admin')->value('id');
        $regularRoleId = Role::where('akses', 'user')->value('id');
        $newUserId = random_int(900000000, 999999999);

        $this->actingAs($staff)
            ->post(route('admin.user.store'), [
                'id' => $newUserId,
                'username' => 'pegawai' . $newUserId,
                'name' => 'Pegawai Test',
                'email' => 'pegawai' . $newUserId . '@example.test',
                'jenis_kelamin' => 'Perempuan',
                'role_id' => $adminRoleId,
                'departemen_id' => Departemen::query()->value('id'),
                'password' => 'password-test',
                'confirm_password' => 'password-test',
            ])
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $newUserId,
            'role_id' => $regularRoleId,
        ]);
    }

    public function test_staff_can_update_and_delete_regular_users(): void
    {
        $staff = $this->makeUser('staff', 'staff');
        $userToUpdate = $this->makeUser('user', 'update');
        $userToDelete = $this->makeUser('user', 'delete');

        $this->actingAs($staff)
            ->put(route('admin.user.update'), [
                'id' => $userToUpdate->id,
                'username' => $userToUpdate->username,
                'name' => 'User Updated By Staff',
                'email' => $userToUpdate->email,
                'jenis_kelamin' => $userToUpdate->jenis_kelamin,
                'role_id' => Role::where('akses', 'admin')->value('id'),
                'departemen_id' => $userToUpdate->departemen_id,
                'type_id' => $userToUpdate->type_id,
            ])
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $userToUpdate->id,
            'name' => 'User Updated By Staff',
            'role_id' => Role::where('akses', 'user')->value('id'),
        ]);

        $this->actingAs($staff)
            ->delete(route('admin.user.delete'), ['id' => $userToDelete->id])
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_staff_cannot_change_attendance_privileged_accounts_or_admin_master_data(): void
    {
        $staff = $this->makeUser('staff', 'staff');
        $admin = $this->makeUser('admin', 'admin');

        $this->actingAs($staff)
            ->post(route('admin.absensi.store'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->put(route('admin.user.update'), ['id' => $admin->id])
            ->assertForbidden();

        $this->actingAs($staff)
            ->delete(route('admin.user.delete'), ['id' => $admin->id])
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('admin.role.index'))
            ->assertForbidden();
    }

    public function test_staff_can_only_update_their_own_profile(): void
    {
        $staff = $this->makeUser('staff', 'staff');
        $otherUser = $this->makeUser('user', 'target');
        $newName = 'Staff Updated';

        $this->actingAs($staff)
            ->put(route('admin.profile.update'), [
                'id' => $otherUser->id,
                'username' => $staff->username,
                'name' => $newName,
                'email' => $staff->email,
                'jenis_kelamin' => $staff->jenis_kelamin,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'name' => $newName,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $otherUser->id,
            'name' => $otherUser->name,
        ]);
    }

    public function test_regular_user_cannot_enter_admin_area(): void
    {
        $user = $this->makeUser('user', 'user');

        $this->actingAs($user)
            ->get(route('admin.dashboard.index'))
            ->assertForbidden();
    }
}
