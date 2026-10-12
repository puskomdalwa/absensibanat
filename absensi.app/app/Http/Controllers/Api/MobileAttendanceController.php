<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Device;
use App\Models\GeofenceLocation;
use App\Models\Kategori;
use App\Models\Keterangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MobileAttendanceController extends Controller
{
    /**
     * Hitung jarak antara 2 koordinat dengan Haversine Formula (dalam meter)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latFrom = deg2rad((float)$lat1);
        $lonFrom = deg2rad((float)$lon1);
        $latTo = deg2rad((float)$lat2);
        $lonTo = deg2rad((float)$lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Cek apakah koordinat user berada di dalam salah satu geofence aktif
     */
    private function validateGeofence($userLat, $userLon)
    {
        $locations = GeofenceLocation::where('is_active', true)->get();

        if ($locations->isEmpty()) {
            return ['valid' => true, 'location' => null, 'distance' => 0];
        }

        $minDistance = null;
        $matchedLocation = null;

        foreach ($locations as $loc) {
            $dist = $this->calculateDistance($userLat, $userLon, $loc->latitude, $loc->longitude);
            if ($minDistance === null || $dist < $minDistance) {
                $minDistance = $dist;
            }

            if ($dist <= $loc->radius_meter) {
                return [
                    'valid' => true,
                    'location' => $loc,
                    'distance' => round($dist, 1)
                ];
            }
        }

        return [
            'valid' => false,
            'location' => null,
            'distance' => round($minDistance, 1)
        ];
    }

    /**
     * Simpan file foto selfie ke public/uploads/absensi_mobile
     */
    private function saveSelfiePhoto(Request $request, $fieldName, $prefix = 'selfie_')
    {
        $destinationPath = public_path('uploads/absensi_mobile');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            $filename = $prefix . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            return 'uploads/absensi_mobile/' . $filename;
        }

        $base64Image = $request->input($fieldName);
        if ($base64Image && is_string($base64Image) && str_contains($base64Image, 'base64,')) {
            $imageParts = explode(';base64,', $base64Image);
            $imageBase64 = base64_decode($imageParts[1]);
            $filename = $prefix . time() . '_' . Str::random(8) . '.jpg';
            file_put_contents($destinationPath . '/' . $filename, $imageBase64);
            return 'uploads/absensi_mobile/' . $filename;
        }

        return null;
    }

    /**
     * Absen Masuk (Check-In)
     */
    public function checkIn(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required',
                'longitude' => 'required',
            ]);

            $user = $request->user();
            $today = Carbon::today()->format('Y-m-d');
            $currentTime = Carbon::now()->format('H:i:s');

            // 1. Validasi Geofence GPS
            $geoCheck = $this->validateGeofence($request->latitude, $request->longitude);
            if (!$geoCheck['valid']) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda berada di luar radius lokasi absensi (' . $geoCheck['distance'] . ' meter dari titik terdekat). Mohon berada di lokasi kampus/asrama.',
                    'distance' => $geoCheck['distance']
                ], 422);
            }

            // 2. Cek apakah sudah absen masuk hari ini
            $absensi = Absensi::where('users_id', $user->id)
                ->where('tgl_absen', $today)
                ->first();

            $hasPagi = $absensi && !empty($absensi->pagi) && $absensi->pagi !== '-';
            if ($hasPagi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah melakukan absen masuk hari ini pada pukul ' . substr($absensi->pagi, 0, 5) . ' WIB.',
                    'data' => $absensi
                ], 400);
            }

            // 3. Simpan Foto Selfie
            $photoPath = $this->saveSelfiePhoto($request, 'foto', 'masuk_');

            // 4. Device ID
            $device = Device::where('name', 'Aplikasi Mobile Banat')->first();
            $deviceId = $device ? $device->id : (Device::first()->id ?? 1);

            // 5. Default Kategori (Ambil kategori dengan selisih terendah)
            $defaultKategori = Kategori::orderBy('selisih', 'asc')->first();
            $kategoriId = $defaultKategori ? $defaultKategori->id : null;

            if (!$absensi) {
                $absensi = new Absensi();
                $absensi->users_id = $user->id;
                $absensi->tgl_absen = $today;
            }

            $absensi->pagi = $currentTime;
            $absensi->latitude = $request->latitude;
            $absensi->longitude = $request->longitude;
            $absensi->verify_id = 4; // Face verification
            $absensi->device_id = $deviceId;
            $absensi->kategori_id = $kategoriId;
            if ($photoPath) {
                $absensi->foto_masuk = $photoPath;
            }
            $absensi->save();

            return response()->json([
                'status' => true,
                'message' => 'Alhamdulillah, absen masuk berhasil dicatat pada pukul ' . substr($currentTime, 0, 5) . ' WIB!',
                'data' => $absensi,
                'location' => $geoCheck['location'] ? $geoCheck['location']->nama_lokasi : 'Area Kampus/Asrama',
                'distance' => $geoCheck['distance']
            ]);
        } catch (\Throwable $th) {
            Log::error('CheckIn Error: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Absen Pulang (Check-Out)
     */
    public function checkOut(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required',
                'longitude' => 'required',
            ]);

            $user = $request->user();
            $today = Carbon::today()->format('Y-m-d');
            $currentTime = Carbon::now()->format('H:i:s');

            // 1. Validasi Geofence GPS
            $geoCheck = $this->validateGeofence($request->latitude, $request->longitude);
            if (!$geoCheck['valid']) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda berada di luar radius lokasi absensi (' . $geoCheck['distance'] . ' meter dari titik terdekat). Mohon berada di lokasi untuk absen pulang.',
                    'distance' => $geoCheck['distance']
                ], 422);
            }

            // 2. Ambil absensi hari ini
            $absensi = Absensi::where('users_id', $user->id)
                ->where('tgl_absen', $today)
                ->first();

            $hasPagi = $absensi && !empty($absensi->pagi) && $absensi->pagi !== '-';
            $hasSore = $absensi && !empty($absensi->sore) && $absensi->sore !== '-';

            if (!$hasPagi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda belum melakukan absen masuk hari ini. Silakan absen masuk terlebih dahulu.',
                ], 400);
            }

            if ($hasSore) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah melakukan absen pulang hari ini pada pukul ' . substr($absensi->sore, 0, 5) . ' WIB.',
                    'data' => $absensi
                ], 400);
            }

            // 3. Simpan Foto Selfie Pulang
            $photoPath = $this->saveSelfiePhoto($request, 'foto', 'pulang_');

            // 4. Hitung durasi kerja / selisih waktu dalam menit
            $rawPagi = $absensi->getRawOriginal('pagi') ?: $absensi->pagi;
            try {
                $pagiTime = Carbon::parse($rawPagi);
                $nowTime = Carbon::now();
                $selisihMenit = max(0, $pagiTime->diffInMinutes($nowTime));
            } catch (\Throwable $t) {
                $selisihMenit = 0;
            }

            // Tentukan Kategori berdasarkan selisih menit
            $matchedKategori = Kategori::where('selisih', '<=', $selisihMenit)
                ->orderBy('selisih', 'desc')
                ->first();

            $absensi->sore = $currentTime;
            $absensi->selisih = $selisihMenit;
            if ($matchedKategori) {
                $absensi->kategori_id = $matchedKategori->id;
            }
            if ($photoPath) {
                $absensi->foto_pulang = $photoPath;
            }
            $absensi->save();

            $durasiJam = floor($selisihMenit / 60);
            $durasiSisaMenit = $selisihMenit % 60;

            return response()->json([
                'status' => true,
                'message' => 'Alhamdulillah, absen pulang berhasil dicatat pada pukul ' . substr($currentTime, 0, 5) . " WIB! Total durasi hadir: {$durasiJam} jam {$durasiSisaMenit} menit.",
                'data' => $absensi,
                'durasi' => "{$durasiJam}j {$durasiSisaMenit}m"
            ]);
        } catch (\Throwable $th) {
            Log::error('CheckOut Error: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil Status Absensi Hari Ini untuk Pengguna Login
     */
    public function statusToday(Request $request)
    {
        try {
            $user = $request->user();
            $today = Carbon::today()->format('Y-m-d');

            $absensi = Absensi::with(['kategori', 'keterangans'])
                ->where('users_id', $user->id)
                ->where('tgl_absen', $today)
                ->first();

            $status = 'BELUM_ABSEN';
            if ($absensi) {
                $hasPagi = !empty($absensi->pagi) && $absensi->pagi !== '-';
                $hasSore = !empty($absensi->sore) && $absensi->sore !== '-';
                if ($hasPagi && !$hasSore) {
                    $status = 'SUDAH_MASUK';
                } elseif ($hasPagi && $hasSore) {
                    $status = 'SUDAH_PULANG';
                }
            }

            return response()->json([
                'status' => true,
                'current_date' => Carbon::now()->translatedFormat('l, d F Y'),
                'attendance_status' => $status,
                'data' => $absensi
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Rekap Statistik Kehadiran Bulanan Pengguna
     */
    public function summaryMonthly(Request $request)
    {
        try {
            $user = $request->user();
            $month = $request->input('bulan', Carbon::now()->month);
            $year = $request->input('tahun', Carbon::now()->year);

            $absensiList = Absensi::where('users_id', $user->id)
                ->whereMonth('tgl_absen', $month)
                ->whereYear('tgl_absen', $year)
                ->get();

            $totalHadir = $absensiList->filter(function ($item) {
                return !empty($item->pagi) && $item->pagi !== '-';
            })->count();

            $totalLengkap = $absensiList->filter(function ($item) {
                return !empty($item->pagi) && $item->pagi !== '-' && !empty($item->sore) && $item->sore !== '-';
            })->count();

            $totalMenit = $absensiList->sum('selisih');

            $totalIzinSakit = Keterangan::join('absensi', 'absensi.id', '=', 'keterangan.absensi_id')
                ->where('absensi.users_id', $user->id)
                ->whereMonth('absensi.tgl_absen', $month)
                ->whereYear('absensi.tgl_absen', $year)
                ->count();

            $totalJam = floor($totalMenit / 60);

            return response()->json([
                'status' => true,
                'bulan' => (int)$month,
                'tahun' => (int)$year,
                'summary' => [
                    'total_hadir' => $totalHadir,
                    'total_lengkap' => $totalLengkap,
                    'total_jam_kerja' => $totalJam,
                    'total_keterangan' => $totalIzinSakit,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil Catatan Kegiatan / Keterangan untuk absensi tertentu
     */
    public function getKegiatan(Request $request)
    {
        try {
            $absensiId = $request->input('absensi_id');
            if (!$absensiId) {
                $today = Carbon::today()->format('Y-m-d');
                $absensi = Absensi::where('users_id', $request->user()->id)->where('tgl_absen', $today)->first();
                $absensiId = $absensi ? $absensi->id : null;
            }

            if (!$absensiId) {
                return response()->json([
                    'status' => true,
                    'data' => [],
                    'has_keterangan' => false,
                    'total' => 0,
                    'message' => 'Belum ada catatan presensi.'
                ]);
            }

            $list = Keterangan::where('absensi_id', $absensiId)->orderBy('waktu', 'asc')->get();
            return response()->json([
                'status' => true,
                'absensi_id' => (int)$absensiId,
                'has_keterangan' => $list->isNotEmpty(),
                'total' => $list->count(),
                'data' => $list
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Tambah Catatan Kegiatan / Keterangan Presensi
     */
    public function storeKegiatan(Request $request)
    {
        try {
            $request->validate([
                'waktu' => 'required',
                'keterangan' => 'required|string',
            ]);

            $absensiId = $request->input('absensi_id');
            if (!$absensiId) {
                $today = Carbon::today()->format('Y-m-d');
                $absensi = Absensi::where('users_id', $request->user()->id)->where('tgl_absen', $today)->first();
                if (!$absensi) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Silakan lakukan absen masuk terlebih dahulu sebelum mengisi kegiatan.'
                    ], 400);
                }
                $absensiId = $absensi->id;
            }

            $waktu = $request->waktu;
            if (strlen($waktu) == 5) {
                $waktu .= ':00';
            }

            $ket = new Keterangan();
            $ket->absensi_id = $absensiId;
            $ket->waktu = $waktu;
            $ket->keterangan = $request->keterangan;
            $ket->save();

            return response()->json([
                'status' => true,
                'message' => 'Alhamdulillah, catatan kegiatan berhasil disimpan!',
                'data' => $ket
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Perbarui Catatan Kegiatan / Keterangan Presensi
     */
    public function updateKegiatan(Request $request, $id)
    {
        try {
            $request->validate([
                'waktu' => 'required',
                'keterangan' => 'required|string',
            ]);

            $ket = Keterangan::find($id);
            if (!$ket) {
                return response()->json(['status' => false, 'message' => 'Data kegiatan tidak ditemukan.'], 404);
            }

            $waktu = $request->waktu;
            if (strlen($waktu) == 5) {
                $waktu .= ':00';
            }

            $ket->waktu = $waktu;
            $ket->keterangan = $request->keterangan;
            $ket->save();

            return response()->json([
                'status' => true,
                'message' => 'Catatan kegiatan berhasil diperbarui!',
                'data' => $ket
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Hapus Catatan Kegiatan
     */
    public function deleteKegiatan(Request $request, $id)
    {
        try {
            $ket = Keterangan::find($id);
            if (!$ket) {
                return response()->json(['status' => false, 'message' => 'Data kegiatan tidak ditemukan.'], 404);
            }
            $ket->delete();

            return response()->json([
                'status' => true,
                'message' => 'Catatan kegiatan berhasil dihapus.'
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
