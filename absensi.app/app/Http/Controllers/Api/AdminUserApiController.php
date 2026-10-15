<?php

namespace App\Http\Controllers\Api;

use App\Exports\UserAbsensiDetailExport;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AdminUserApiController extends Controller
{
    /**
     * Dapatkan daftar roles yang tersedia
     */
    public function getRoles(Request $request)
    {
        $roles = Role::orderBy('id', 'asc')->get(['id', 'akses']);
        return response()->json([
            'status' => true,
            'message' => 'Berhasil memuat daftar role',
            'data' => $roles
        ]);
    }

    /**
     * Dapatkan daftar user untuk admin/superadmin
     */
    public function getUsers(Request $request)
    {
        $search = $request->input('search');
        $roleId = $request->input('role_id');

        $query = User::with(['role', 'departemen'])
            ->select('id', 'username', 'name', 'email', 'role_id', 'departemen_id', 'photo', 'kode');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $authUser = $request->user();
        $isSuperAdmin = false;
        if ($authUser) {
            if (method_exists($authUser, 'isSuperAdmin') && $authUser->isSuperAdmin()) {
                $isSuperAdmin = true;
            } elseif (isset($authUser->role) && strtolower($authUser->role->akses) === 'superadmin') {
                $isSuperAdmin = true;
            } elseif ($authUser->role_id == 1) {
                $isSuperAdmin = true;
            }
        }

        // Jika bukan superadmin, user dengan role superadmin disembunyikan
        if (!$isSuperAdmin) {
            $query->whereHas('role', function ($r) {
                $r->where('akses', 'not like', '%superadmin%');
            });
        }

        if ($roleId && $roleId !== '*') {
            $query->where('role_id', $roleId);
        }

        $users = $query->orderBy('name', 'asc')->paginate(50);

        return response()->json([
            'status' => true,
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    /**
     * Update role dan profil user
     */
    public function updateUserRole(Request $request, $id)
    {
        $authUser = $request->user();
        $isSuperAdmin = $authUser->isSuperAdmin();
        $isAdmin = $authUser->isAdmin();

        if (!$isAdmin && !$isSuperAdmin) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak memiliki wewenang untuk mengubah role user.'
            ], 403);
        }

        $user = User::findOrFail($id);

        if (!$isSuperAdmin && $user->isSuperAdmin()) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak memiliki hak untuk mengubah akun Superadmin.'
            ], 403);
        }

        $request->validate([
            'role_id' => 'required|exists:role,id',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
        ]);

        $chosenRole = Role::find($request->role_id);
        if (!$isSuperAdmin) {
            $targetRoleName = strtolower($chosenRole->akses ?? '');
            if ($targetRoleName === 'superadmin' || $targetRoleName === 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'Admin tidak dapat menetapkan role Admin maupun Superadmin. Hanya Superadmin yang berwenang.'
                ], 403);
            }
        }

        $user->role_id = $request->role_id;
        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        if ($request->filled('departemen_id')) {
            $user->departemen_id = $request->departemen_id;
        }
        $user->save();

        return response()->json([
            'status' => true,
            'message' => "Role {$user->name} berhasil diperbarui menjadi {$chosenRole->akses}.",
            'data' => $user->load(['role', 'departemen'])
        ]);
    }

    /**
     * Detail Absensi User (Persis seperti /admin/user/{id}/absensi di web)
     */
    public function getUserAbsensi(Request $request, $id)
    {
        $authUser = $request->user();
        $isExecutive = false;
        if ($authUser) {
            $roleAkses = strtolower($authUser->role ? ($authUser->role->akses ?? '') : '');
            if (in_array($authUser->role_id, [1, 2]) || in_array($roleAkses, ['admin', 'superadmin'])) {
                $isExecutive = true;
            }
        }
        if (!$isExecutive && $authUser) {
            $id = $authUser->id; // Strict security: non-admin can only see own detail absensi
        }

        $user = User::with(['role', 'departemen'])->findOrFail($id);

        $report = $this->buildUserAbsensiReport($user, $request);

        return response()->json([
            'status' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'photo' => $user->photo,
                'role' => $user->role ? $user->role->akses : 'User',
                'role_id' => $user->role_id,
                'departemen' => $user->departemen ? $user->departemen->nama : '-',
            ],
            'period_label' => $report['period_label'],
            'summary' => $report['summary'],
            'categories' => $report['categories'],
            'daily' => $report['daily'],
            'details' => $report['details'],
        ]);
    }

    /**
     * Export Excel Detail Absensi User
     */
    public function exportUserAbsensiExcel(Request $request, $id)
    {
        $authUser = $request->user();
        $isExecutive = false;
        if ($authUser) {
            $roleAkses = strtolower($authUser->role ? ($authUser->role->akses ?? '') : '');
            if (in_array($authUser->role_id, [1, 2]) || in_array($roleAkses, ['admin', 'superadmin'])) {
                $isExecutive = true;
            }
        }
        if (!$isExecutive && $authUser) {
            $id = $authUser->id; // Strict security: non-admin can only export own detail absensi
        }

        $user = User::with(['role', 'departemen'])->findOrFail($id);
        $report = $this->buildUserAbsensiReport($user, $request);

        $filename = 'Detail_Absensi_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $user->name) . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new UserAbsensiDetailExport($report, $report['show_nominal'] ?? Setting::isNominalVisible($authUser)),
            $filename
        );
    }

    /**
     * Helper membuat laporan detail absensi user
     */
    private function buildUserAbsensiReport(User $user, Request $request): array
    {
        $query = DB::table('absensi')
            ->leftJoin('kategori', 'kategori.id', '=', 'absensi.kategori_id')
            ->select(
                'absensi.id',
                'absensi.tgl_absen',
                'absensi.pagi',
                'absensi.sore',
                'absensi.selisih',
                'absensi.latitude',
                'absensi.longitude',
                'absensi.foto_masuk',
                'absensi.foto_pulang',
                'absensi.keterangan',
                'absensi.kategori_id',
                'kategori.nama as kategori_nama',
                'kategori.kode as kategori_kode',
                'kategori.nominal as kategori_nominal'
            )
            ->where('absensi.users_id', $user->id);

        $filterType = $request->input('filter_type', 'semua');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $bulan = $request->input('bulan', '*');
        $tahun = $request->input('tahun', '*');
        $kategoriId = $request->input('kategori_id', '*');

        if ($filterType === 'rentang_tanggal' || ($startDate && $endDate)) {
            $query->whereBetween('absensi.tgl_absen', [$startDate, $endDate]);
        } elseif ($filterType === 'bulan_tahun' || ($bulan !== '*' || $tahun !== '*')) {
            if ($bulan !== '*') {
                $query->whereMonth('absensi.tgl_absen', $bulan);
            }
            if ($tahun !== '*') {
                $query->whereYear('absensi.tgl_absen', $tahun);
            }
        }

        if ($kategoriId && $kategoriId !== '*') {
            $query->where('absensi.kategori_id', $kategoriId);
        }

        $rows = $query->orderBy('absensi.tgl_absen', 'desc')->get();
        $showNominal = Setting::isNominalVisible($request->user());

        // Kategori list
        $kategoriList = Kategori::orderBy('kode')->get();
        $summaryByCategory = [];
        foreach ($kategoriList as $k) {
            $summaryByCategory[$k->id] = [
                'id' => $k->id,
                'kode' => $k->kode,
                'nama' => $k->nama,
                'nominal_per_absensi' => $showNominal ? (float)$k->nominal : 0,
                'nominal_per_absensi_formatted' => $showNominal ? ('Rp ' . number_format((float)$k->nominal, 0, ',', '.')) : '-',
                'total_absensi' => 0,
                'total_nominal' => 0,
                'total_nominal_formatted' => $showNominal ? 'Rp 0' : '-',
                'total_menit' => 0,
                'durasi_teks' => '-',
            ];
        }

        $totalMenit = 0;
        $totalNominal = 0;
        $details = [];
        $daily = [];

        // Fetch keterangans for these absensi IDs
        $absensiIds = $rows->pluck('id')->filter()->all();
        $keterangansByAbsensi = [];
        if (!empty($absensiIds)) {
            $kets = DB::table('keterangan')->whereIn('absensi_id', $absensiIds)->get();
            foreach ($kets as $ket) {
                $keterangansByAbsensi[$ket->absensi_id][] = $ket;
            }
        }

        foreach ($rows as $index => $row) {
            $hasCheckIn = !empty($row->pagi) && $row->pagi !== '-';
            $rowKeterangans = $keterangansByAbsensi[$row->id] ?? [];

            $menit = (int)($row->selisih ?? 0);
            if ($menit <= 0 && $row->pagi && $row->sore) {
                try {
                    $pagi = Carbon::parse($row->pagi);
                    $sore = Carbon::parse($row->sore);
                    $menit = max(0, $pagi->diffInMinutes($sore));
                } catch (\Throwable $t) {
                    $menit = 0;
                }
            }

            if (!$hasCheckIn) {
                // SAKIT / IZIN / TIDAK HADIR: Pendapatan hari itu 0, BUKAN kategori C!
                $menit = 0;
                $nominal = 0;
                $durasiTeks = '-';

                $ketText = strtolower((string)($row->keterangan ?? ''));
                if (empty($ketText) && !empty($rowKeterangans)) {
                    $firstKet = reset($rowKeterangans);
                    $ketText = strtolower((string)($firstKet->keterangan ?? ''));
                }

                if (str_contains($ketText, 'sakit')) {
                    $kategoriNama = 'Sakit';
                } elseif (str_contains($ketText, 'izin')) {
                    $kategoriNama = 'Izin';
                } elseif (!empty($ketText)) {
                    $kategoriNama = 'Izin / Keterangan';
                } else {
                    $kategoriNama = 'Tidak Hadir';
                }

                $kategoriTeks = $kategoriNama;
            } else {
                $durasiJam = floor($menit / 60);
                $durasiSisa = $menit % 60;
                $durasiTeks = $menit > 0 ? "{$durasiJam}j {$durasiSisa}m" : '-';

                $nominal = (float)($row->kategori_nominal ?? 0);
                $kategoriKey = $row->kategori_id ?: 'tanpa_kategori';

                if (!isset($summaryByCategory[$kategoriKey])) {
                    $summaryByCategory[$kategoriKey] = [
                        'id' => $row->kategori_id,
                        'kode' => $row->kategori_kode ?: '-',
                        'nama' => $row->kategori_nama ?: 'Tanpa Kategori',
                        'nominal_per_absensi' => $nominal,
                        'nominal_per_absensi_formatted' => 'Rp ' . number_format($nominal, 0, ',', '.'),
                        'total_absensi' => 0,
                        'total_nominal' => 0,
                        'total_nominal_formatted' => 'Rp 0',
                        'total_menit' => 0,
                        'durasi_teks' => '-',
                    ];
                }

                $summaryByCategory[$kategoriKey]['total_absensi']++;
                $summaryByCategory[$kategoriKey]['total_nominal'] += $nominal;
                $summaryByCategory[$kategoriKey]['total_menit'] += $menit;

                $totalMenit += $menit;
                $totalNominal += $nominal;

                $kategoriTeks = trim(($row->kategori_kode ?: '-') . ' - ' . ($row->kategori_nama ?: 'Tanpa Kategori'));
                $kategoriNama = $row->kategori_nama ?: 'Hadir';
            }

            $details[] = [
                'no' => $index + 1,
                'id' => $row->id,
                'tanggal' => $row->tgl_absen,
                'tanggal_formatted' => Carbon::parse($row->tgl_absen)->translatedFormat('l, d F Y'),
                'jam_datang' => $row->pagi ? substr($row->pagi, 0, 5) . ' WIB' : '-',
                'jam_pulang' => $row->sore ? substr($row->sore, 0, 5) . ' WIB' : '-',
                'pagi_raw' => $row->pagi,
                'sore_raw' => $row->sore,
                'kategori' => $kategoriTeks,
                'kategori_nama' => $kategoriNama,
                'durasi_menit' => $menit,
                'durasi_teks' => $durasiTeks,
                'nominal' => $showNominal ? $nominal : 0,
                'nominal_formatted' => $showNominal ? ('Rp ' . number_format($nominal, 0, ',', '.')) : '-',
                'keterangan' => $row->keterangan ?: '-',
                'keterangans_count' => count($rowKeterangans),
                'keterangans' => $rowKeterangans,
                'foto_masuk' => $row->foto_masuk,
                'foto_pulang' => $row->foto_pulang,
                'latitude' => $row->latitude,
                'longitude' => $row->longitude,
            ];
        }

        // Format summary totals
        foreach ($summaryByCategory as $key => &$item) {
            $item['total_nominal_formatted'] = $showNominal ? ('Rp ' . number_format($item['total_nominal'], 0, ',', '.')) : '-';
            if (! $showNominal) {
                $item['nominal_per_absensi'] = 0;
                $item['total_nominal'] = 0;
            }
            $h = floor($item['total_menit'] / 60);
            $m = $item['total_menit'] % 60;
            $item['durasi_teks'] = $item['total_menit'] > 0 ? "{$h}j {$m}m" : '-';
        }

        $totHours = floor($totalMenit / 60);
        $totMins = $totalMenit % 60;
        $totalDurasiTeks = $totalMenit > 0 ? "{$totHours}j {$totMins}m" : '-';
        $totalCount = count($rows);
        $rataNominal = ($showNominal && $totalCount > 0) ? round($totalNominal / $totalCount) : 0;

        $periodLabel = 'Semua Periode';
        if ($filterType === 'bulan_tahun' && $bulan !== '*' && $tahun !== '*') {
            $periodLabel = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');
        } elseif ($startDate && $endDate) {
            $periodLabel = Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
        }

        return [
            'period_label' => $periodLabel,
            'summary' => [
                'total_absensi' => $totalCount,
                'total_nominal' => $showNominal ? $totalNominal : 0,
                'total_nominal_formatted' => $showNominal ? ('Rp ' . number_format($totalNominal, 0, ',', '.')) : '-',
                'total_menit' => $totalMenit,
                'total_durasi_teks' => $totalDurasiTeks,
                'rata_nominal' => $rataNominal,
                'rata_nominal_formatted' => ($showNominal && $totalCount > 0) ? ('Rp ' . number_format($rataNominal, 0, ',', '.')) : '-',
            ],
            'categories' => array_values($summaryByCategory),
            'daily' => $daily,
            'details' => $details,
            'user' => $user,
            'show_nominal' => $showNominal,
        ];
    }
}