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
use Illuminate\Support\Facades\DB;
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

            $authUser = $request->user();
            $isExecutive = false;
            if ($authUser) {
                $roleName = strtolower($authUser->role ? ($authUser->role->akses ?? '') : '');
                $isExecutive = in_array($roleName, ['admin', 'superadmin']) || ($authUser->role_id == 1 || $authUser->role_id == 2);
            }

            $absensiId = $request->input('absensi_id');
            if (!$absensiId) {
                $today = Carbon::today()->format('Y-m-d');
                $absensi = Absensi::where('users_id', $authUser->id)->where('tgl_absen', $today)->first();
                if (!$absensi) {
                    $device = Device::where('name', 'Aplikasi Mobile Banat')->first();
                    $deviceId = $device ? $device->id : (Device::first()->id ?? 1);
                    $defaultKategori = Kategori::orderBy('selisih', 'asc')->first();

                    $absensi = new Absensi();
                    $absensi->users_id = $authUser->id;
                    $absensi->tgl_absen = $today;
                    $absensi->verify_id = 1; // Manual / Dispensasi
                    $absensi->device_id = $deviceId;
                    $absensi->kategori_id = null; // Sakit / izin tanpa absen masuk tidak berbayar (nominal 0)
                    $absensi->keterangan = $request->keterangan;
                    $absensi->save();
                }
                $absensiId = $absensi->id;
            } else {
                // If not executive, verify that this attendance belongs to authUser
                if (!$isExecutive && $authUser) {
                    $ownerCheck = Absensi::where('id', $absensiId)->where('users_id', $authUser->id)->exists();
                    if (!$ownerCheck) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Anda hanya berhak mengisi catatan kegiatan pada presensi Anda sendiri.'
                        ], 403);
                    }
                }
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
                'message' => 'Catatan kegiatan berhasil ditambahkan!',
                'data' => $ket
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update Catatan Kegiatan
     */
    public function updateKegiatan(Request $request, $id)
    {
        try {
            $request->validate([
                'waktu' => 'required',
                'keterangan' => 'required|string',
            ]);

            $authUser = $request->user();
            $isExecutive = false;
            if ($authUser) {
                $roleName = strtolower($authUser->role ? ($authUser->role->akses ?? '') : '');
                $isExecutive = in_array($roleName, ['admin', 'superadmin']) || ($authUser->role_id == 1 || $authUser->role_id == 2);
            }

            $ket = Keterangan::find($id);
            if (!$ket) {
                return response()->json(['status' => false, 'message' => 'Data kegiatan tidak ditemukan.'], 404);
            }

            // Ownership check
            if (!$isExecutive && $authUser) {
                $ownerCheck = Absensi::where('id', $ket->absensi_id)->where('users_id', $authUser->id)->exists();
                if (!$ownerCheck) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Anda hanya berhak mengubah catatan kegiatan Anda sendiri.'
                    ], 403);
                }
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
            $authUser = $request->user();
            $isExecutive = false;
            if ($authUser) {
                $roleName = strtolower($authUser->role ? ($authUser->role->akses ?? '') : '');
                $isExecutive = in_array($roleName, ['admin', 'superadmin']) || ($authUser->role_id == 1 || $authUser->role_id == 2);
            }

            $ket = Keterangan::find($id);
            if (!$ket) {
                return response()->json(['status' => false, 'message' => 'Data kegiatan tidak ditemukan.'], 404);
            }

            // Ownership check
            if (!$isExecutive && $authUser) {
                $ownerCheck = Absensi::where('id', $ket->absensi_id)->where('users_id', $authUser->id)->exists();
                if (!$ownerCheck) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Anda hanya berhak menghapus catatan kegiatan Anda sendiri.'
                    ], 403);
                }
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
    /**
     * Leaderboard Kehadiran Paling Awal (Early Bird) & Konsistensi
     * - Periode today: Peringkat yang masuk paling pagi hari ini (mulai jam wajar >= 04:30:00)
     * - Periode month: Peringkat dengan kehadiran terbanyak & rata-rata jam masuk paling awal
     * - Mengecualikan superadmin agar netral dan adil
     */
    public function getLeaderboard(Request $request)
    {
        try {
            $authUser = $request->user();
            $periode = $request->input('periode', 'today'); // 'today' or 'month'
            $roleFilter = $request->input('role_id', '*');

            if ($periode === 'month') {
                $month = $request->input('month', Carbon::now()->month);
                $year = $request->input('year', Carbon::now()->year);

                $query = DB::table('absensi')
                    ->join('users', 'users.id', '=', 'absensi.users_id')
                    ->leftJoin('role', 'role.id', '=', 'users.role_id')
                    ->leftJoin('departemen', 'departemen.id', '=', 'users.departemen_id')
                    ->whereMonth('absensi.tgl_absen', $month)
                    ->whereYear('absensi.tgl_absen', $year)
                    ->whereNotNull('absensi.pagi')
                    ->where('absensi.pagi', '>=', '04:30:00')
                    ->where(function ($q) {
                        $q->whereNull('role.akses')
                          ->orWhereRaw('LOWER(role.akses) NOT LIKE ?', ['%superadmin%']);
                    })
                    ->where(function ($q) {
                        $q->whereNull('users.role_id')
                          ->orWhere('users.role_id', '!=', 16);
                    });

                if ($roleFilter !== '*' && !empty($roleFilter)) {
                    $query->where('users.role_id', $roleFilter);
                }

                $grouped = $query->groupBy('users.id', 'users.name', 'users.username', 'users.photo', 'role.akses', 'departemen.nama')
                    ->select(
                        'users.id as user_id',
                        'users.name',
                        'users.username',
                        'users.photo',
                        'role.akses as role_name',
                        'departemen.nama as departemen_name',
                        DB::raw('COUNT(absensi.id) as total_hadir'),
                        DB::raw('AVG(TIME_TO_SEC(absensi.pagi)) as avg_detik'),
                        DB::raw('MIN(absensi.pagi) as earliest_time')
                    )
                    ->orderBy('total_hadir', 'desc')
                    ->orderBy('avg_detik', 'asc')
                    ->get();

                $leaderboard = [];
                $myRank = null;

                foreach ($grouped as $index => $item) {
                    $rank = $index + 1;
                    $avgSec = (int)$item->avg_detik;
                    $avgH = floor($avgSec / 3600);
                    $avgM = floor(($avgSec % 3600) / 60);
                    $avgTimeFormatted = sprintf('%02d:%02d WIB', $avgH, $avgM);

                    $isMe = ($authUser && $authUser->id == $item->user_id);

                    $dataItem = [
                        'rank' => $rank,
                        'user_id' => $item->user_id,
                        'name' => $item->name,
                        'username' => $item->username,
                        'photo' => $item->photo,
                        'role' => strtoupper($item->role_name ?: 'USER'),
                        'departemen' => $item->departemen_name ?: '-',
                        'total_hadir' => (int)$item->total_hadir,
                        'avg_jam_masuk' => $avgTimeFormatted,
                        'earliest_jam' => $item->earliest_time ? substr($item->earliest_time, 0, 5) . ' WIB' : '-',
                        'is_me' => $isMe,
                    ];

                    if ($rank <= 10) {
                        $leaderboard[] = $dataItem;
                    }

                    if ($isMe) {
                        $myRank = [
                            'rank' => $rank,
                            'total_hadir' => (int)$item->total_hadir,
                            'avg_jam_masuk' => $avgTimeFormatted,
                            'has_checked_in' => true,
                            'motivational_text' => $rank <= 3
                                ? 'Luar biasa! Anda berada di podium kehormatan bulan ini! 🏆'
                                : 'Terus pertahankan kedisiplinan Anda hingga akhir bulan! 🌟',
                        ];
                    }
                }

                if (!$myRank && $authUser) {
                    $myRank = [
                        'rank' => null,
                        'total_hadir' => 0,
                        'avg_jam_masuk' => '-',
                        'has_checked_in' => false,
                        'motivational_text' => 'Anda belum memiliki rekam presensi bulan ini. Yuk mulai hadir tepat waktu!',
                    ];
                }

                $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

                return response()->json([
                    'status' => true,
                    'periode' => 'month',
                    'date_label' => "Periode $monthName",
                    'leaderboard' => $leaderboard,
                    'my_rank' => $myRank,
                    'total_peserta' => count($grouped),
                ]);

            } else {
                // PERIODE TODAY (Early Bird Hari Ini)
                $today = Carbon::today()->format('Y-m-d');

                $query = DB::table('absensi')
                    ->join('users', 'users.id', '=', 'absensi.users_id')
                    ->leftJoin('role', 'role.id', '=', 'users.role_id')
                    ->leftJoin('departemen', 'departemen.id', '=', 'users.departemen_id')
                    ->where('absensi.tgl_absen', $today)
                    ->whereNotNull('absensi.pagi')
                    ->where('absensi.pagi', '>=', '04:30:00')
                    ->where(function ($q) {
                        $q->whereNull('role.akses')
                          ->orWhereRaw('LOWER(role.akses) NOT LIKE ?', ['%superadmin%']);
                    })
                    ->where(function ($q) {
                        $q->whereNull('users.role_id')
                          ->orWhere('users.role_id', '!=', 16);
                    });

                if ($roleFilter !== '*' && !empty($roleFilter)) {
                    $query->where('users.role_id', $roleFilter);
                }

                $allRows = $query->orderBy('absensi.pagi', 'asc')
                    ->select(
                        'users.id as user_id',
                        'users.name',
                        'users.username',
                        'users.photo',
                        'role.akses as role_name',
                        'departemen.nama as departemen_name',
                        'absensi.pagi',
                        'absensi.sore',
                        'absensi.selisih'
                    )
                    ->get();

                $leaderboard = [];
                $myRank = null;

                foreach ($allRows as $index => $item) {
                    $rank = $index + 1;
                    $jamMasuk = substr($item->pagi, 0, 5) . ' WIB';
                    $isMe = ($authUser && $authUser->id == $item->user_id);

                    $dataItem = [
                        'rank' => $rank,
                        'user_id' => $item->user_id,
                        'name' => $item->name,
                        'username' => $item->username,
                        'photo' => $item->photo,
                        'role' => strtoupper($item->role_name ?: 'USER'),
                        'departemen' => $item->departemen_name ?: '-',
                        'jam_masuk' => $jamMasuk,
                        'is_me' => $isMe,
                    ];

                    if ($rank <= 10) {
                        $leaderboard[] = $dataItem;
                    }

                    if ($isMe) {
                        $myRank = [
                            'rank' => $rank,
                            'jam_masuk' => $jamMasuk,
                            'has_checked_in' => true,
                            'motivational_text' => $rank <= 3
                                ? 'Hebat! Anda berhasil meraih podium juara pagi hari ini! 🥇'
                                : 'Bagus sekali! Anda berhasil masuk jajaran pagi hari ini. 🚀',
                        ];
                    }
                }

                if (!$myRank && $authUser) {
                    $myRank = [
                        'rank' => null,
                        'jam_masuk' => '-',
                        'has_checked_in' => false,
                        'motivational_text' => 'Anda belum presensi masuk hari ini. Yuk presensi lebih awal besok pagi!',
                    ];
                }

                $todayFormatted = Carbon::today()->translatedFormat('l, d F Y');

                return response()->json([
                    'status' => true,
                    'periode' => 'today',
                    'date_label' => $todayFormatted,
                    'leaderboard' => $leaderboard,
                    'my_rank' => $myRank,
                    'total_peserta' => count($allRows),
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memuat data leaderboard: ' . $th->getMessage(),
            ], 500);
        }
    }
}
