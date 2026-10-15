<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * Get a setting by key with fallback default.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        try {
            if (! Schema::hasTable('settings')) {
                return $default;
            }

            return Cache::remember('app_setting_' . $key, 60, function () use ($key, $default) {
                $setting = static::where('key', $key)->first();
                if (! $setting) {
                    return $default;
                }

                return static::castValue($setting->value, $setting->type);
            });
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Set / update a setting by key.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @param string|null $description
     * @return static
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general', ?string $description = null): static
    {
        $serializedValue = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $serializedValue,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );

        Cache::forget('app_setting_' . $key);

        return $setting;
    }

    /**
     * Cast raw value to its appropriate data type.
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected static function castValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        return match (strtolower($type)) {
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $value,
            'float', 'double' => (float) $value,
            'json', 'array' => json_decode($value, true),
            default => (string) $value,
        };
    }

    /**
     * Get the current nominal display mode.
     * Modes:
     * - 'show'       : Tampilkan nominal untuk semua user di semua halaman (default).
     * - 'hide'       : Sembunyikan nominal untuk SEMUA user di SEMUA halaman.
     * - 'admin_only' : Sembunyikan nominal untuk civitas/user biasa, hanya admin & superadmin yang melihat.
     *
     * @return string
     */
    public static function getNominalDisplayMode(): string
    {
        $mode = (string) static::get('nominal_display_mode', 'show');

        return in_array($mode, ['show', 'hide', 'admin_only'], true) ? $mode : 'show';
    }

    /**
     * Set the nominal display mode.
     *
     * @param string $mode
     * @return void
     */
    public static function setNominalDisplayMode(string $mode): void
    {
        if (! in_array($mode, ['show', 'hide', 'admin_only'], true)) {
            $mode = 'show';
        }

        static::set(
            'nominal_display_mode',
            $mode,
            'string',
            'presensi',
            'Mode tampilan nominal honorarium (show, hide, admin_only)'
        );
    }

    /**
     * Check if nominal should be visible for the given user (or auth user).
     *
     * @param mixed $user
     * @return bool
     */
    public static function isNominalVisible($user = null): bool
    {
        try {
            $mode = static::getNominalDisplayMode();

            // Sembunyikan untuk SEMUA user di SEMUA halaman
            if ($mode === 'hide') {
                return false;
            }

            // Sembunyikan untuk civitas biasa, hanya admin & superadmin yang dapat melihat
            if ($mode === 'admin_only') {
                $user = $user ?? Auth::user();
                if (! $user) {
                    return false;
                }

                if (method_exists($user, 'isAdmin')) {
                    return $user->isAdmin();
                }

                $role = strtolower((string) optional($user->role)->akses);
                return in_array($role, ['admin', 'superadmin'], true);
            }

            // 'show' : tampilkan ke semua orang
            return true;
        } catch (\Throwable $e) {
            return true;
        }
    }
}
