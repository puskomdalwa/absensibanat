<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\Contracts\HasApiTokens;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;

class User extends Authenticatable
{
    use SanctumHasApiTokens, HasFactory, Notifiable;

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

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
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
