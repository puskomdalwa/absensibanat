<?php

namespace App\Http\Services;

use App\Models\Absensi;
use App\Models\Kategori;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RealtimeAbsensi
{
    public const MINIMUM_DURASI_PULANG = 120;
    public const KODE_KAT_A = 'KAT-A';
    public const KODE_KAT_B = 'KAT-B';
    public const KODE_KAT_C = 'KAT-C';

    public static function kategoriUntukDurasi(int $menit, ?Collection $kategoriList = null): ?Kategori
    {
        // Ambil semua kategori diurutkan dari selisih terbesar ke terkecil
        // Kecualikan SANTRI agar tidak bentrok dengan kategori umum KAT-C yang sama-sama selisih 0
        if (!$kategoriList) {
            $kategoriList = Kategori::where('kode', '!=', 'SANTRI')->orderBy('selisih', 'desc')->get();
        }

        foreach ($kategoriList as $kat) {
            if ($menit >= $kat->selisih) {
                return $kat;
            }
        }

        // Fallback jika tidak ada yang cocok (seharusnya yang selisih 0 akan selalu cocok)
        return $kategoriList->firstWhere('kode', self::KODE_KAT_C);
    }

    public static function isiKategori(Absensi $absensi, Carbon $scanTime): void
    {
        $menit = self::selisihMenit($absensi->tgl_absen, $absensi->pagi, $absensi->sore);
        $absensi->selisih = $menit;

        $isSantri = false;
        $user = $absensi->user ?: User::find($absensi->users_id);
        if ($user && $user->type && strcasecmp($user->type->nama, 'santri') === 0) {
            $isSantri = true;
        }

        if ($isSantri) {
            $kategori = Kategori::where('kode', 'SANTRI')->first();
        } else {
            $kategori = self::kategoriUntukDurasi($menit);
        }

        if ($kategori) {
            $absensi->kategori_id = $kategori->id;
        }
    }

    public static function selisihMenit(?string $tanggal, ?string $mulai, ?string $akhir): int
    {
        if (!$tanggal || !$mulai || !$akhir || $mulai === '-' || $akhir === '-') {
            return 0;
        }

        $awal = Carbon::parse($tanggal . ' ' . $mulai);
        $selesai = Carbon::parse($tanggal . ' ' . $akhir);

        if ($selesai->lt($awal)) {
            return 0;
        }

        return $awal->diffInMinutes($selesai);
    }

    public static function memenuhiDurasiMinimumPulang(
        ?string $tanggal,
        ?string $jamDatang,
        ?string $jamPulang
    ): bool {
        return self::selisihMenit($tanggal, $jamDatang, $jamPulang) >= self::MINIMUM_DURASI_PULANG;
    }

    public static function formatMenit(int $menit): string
    {
        if ($menit <= 0) {
            return '-';
        }

        $jam = intdiv($menit, 60);
        $sisaMenit = $menit % 60;

        return trim(($jam ? $jam . ' jam ' : '') . ($sisaMenit ? $sisaMenit . ' menit' : ''));
    }
}
