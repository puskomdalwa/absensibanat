<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\Contracts\HasApiTokens;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;

use App\Models\Absensi;
use App\Models\Keterangan;
use App\Models\FingerspotDeviceUser;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use SanctumHasApiTokens, HasFactory, Notifiable;

    protected static function booted()
    {
        static::deleting(function ($user) {
            // Delete personal access tokens
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            // Delete fingerspot device user
            FingerspotDeviceUser::where('pin', (string)$user->id)->delete();

            // Delete user photo file if exists
            if ($user->photo) {
                $photoPath = public_path('photo/' . $user->photo);
                if (file_exists($photoPath)) {
                    @unlink($photoPath);
                }
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'kode',
        'name',
        'password',
        'email',
        'username',
        'role_id',
        'departemen_id',
        'type_id',
        'photo',
        'jenis_kelamin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];
    //
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string ...$roles): bool
    {
        $currentRole = strtolower((string) optional($this->role)->akses);
        $allowedRoles = array_map('strtolower', $roles);

        return in_array($currentRole, $allowedRoles, true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin', 'superadmin');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    public function scopeCivitasOnly($query)
    {
        return $query->whereDoesntHave('role', function ($q) {
            $q->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(akses)'), ['admin', 'superadmin']);
        });
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'users_id', 'id');
    }

    public function departemen(){
        return $this->belongsTo(Departemen::class);
    }
}
