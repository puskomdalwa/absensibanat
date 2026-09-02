<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\RealtimeAbsensi;
use App\Models\Absensi;
use App\Models\Kategori;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientAbsensiController extends Controller
{
    /**
     * Get Data Absensi Biasa (Daftar Record Absensi dengan filter & paginasi)
     */
    public function index(Request $request)
    {
        $limit = (int) $request->get('limit', 15);
        
        $query = Absensi::join('users', 'users.id', '=', 'absensi.users_id')
            ->leftJoin('departemen', 'departemen.id', '=', 'users.departemen_id')
            ->leftJoin('kategori', 'kategori.id', '=', 'absensi.kategori_id')
            ->select(
                'absensi.*',
                'users.kode as user_kode',
                'users.name as user_name',
                'users.username as user_username',
                'users.email as user_email',
                'departemen.nama as departemen_nama',
                'kategori.kode as kategori_kode',
                'kategori.nama as kategori_nama',
                'kategori.nominal as kategori_nominal'
            );

        // Filter Periode & Tanggal
        $mode = $request->get('mode');
        $startDate = null;
        $endDate = null;
        $bulan = null;
        $tahun = null;

        if ($mode === 'rentang_tanggal' || ($request->filled('start_date') && $request->filled('end_date'))) {
            $mode = 'rentang_tanggal';
            $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
            $query->whereBetween('absensi.tgl_absen', [$startDate, $endDate]);
        } elseif ($mode === 'bulan_tahun' || $request->filled('bulan') || $request->filled('tahun')) {
            $mode = 'bulan_tahun';
            $bulan = (int) $request->get('bulan', now()->month);
            $tahun = (int) $request->get('tahun', now()->year);
            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay()->format('Y-m-d');
            $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');
            $query->whereBetween('absensi.tgl_absen', [$startDate, $endDate]);
        } elseif ($request->filled('start_date')) {
            $query->where('absensi.tgl_absen', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('absensi.tgl_absen', '<=', $request->end_date);
        } elseif ($request->filled('tgl_absen')) {
            $query->where('absensi.tgl_absen', $request->tgl_absen);
        }

        // Filter User
        if ($request->filled('user_id')) {
            $query->where('absensi.users_id', $request->user_id);
        }
        if ($request->filled('kode_user')) {
            $query->where('users.kode', $request->kode_user);
        } elseif ($request->filled('kode')) {
            $query->where('users.kode', $request->kode);
        }

        // Filter Kategori
        if ($request->filled('kategori_id')) {
            $query->where('absensi.kategori_id', $request->kategori_id);
        }

        // Filter Departemen
        if ($request->filled('departemen_id')) {
            $query->where('users.departemen_id', $request->departemen_id);
        }

        // Filter Search
        if ($request->filled('search')) {
            $term = trim((string) $request->search);
            $query->where(function ($q) use ($term) {
                $q->orWhere('users.name', 'LIKE', "%{$term}%");
                $q->orWhere('users.username', 'LIKE', "%{$term}%");
                $q->orWhere('users.kode', 'LIKE', "%{$term}%");
                $q->orWhere('absensi.keterangan', 'LIKE', "%{$term}%");
            });
        }

        $query->orderBy('absensi.tgl_absen', 'desc')->orderBy('absensi.id', 'desc');
        $paginated = $query->paginate($limit);

        $items = collect($paginated->items())->map(function ($row) {
            $selisihMenit = (int) $row->selisih;
            if ($selisihMenit <= 0 && $row->pagi && $row->sore) {
                $selisihMenit = RealtimeAbsensi::selisihMenit($row->tgl_absen, $row->pagi, $row->sore);
            }
            $nominal = (float) ($row->kategori_nominal ?? 0);

            return [
                'id' => $row->id,
                'tgl_absen' => $row->tgl_absen,
                'pagi' => $row->pagi ?: '-',
                'sore' => $row->sore ?: '-',
                'selisih_menit' => $selisihMenit,
                'durasi_jam' => round($selisihMenit / 60, 2),
                'durasi_teks' => RealtimeAbsensi::formatMenit($selisihMenit),
                'keterangan' => $row->keterangan ?: '-',
                'kode_user' => $row->user_kode ?: '-',
                'user' => [
                    'id' => $row->users_id,
                    'kode' => $row->user_kode ?: '-',
                    'kode_user' => $row->user_kode ?: '-',
                    'name' => $row->user_name,
                    'username' => $row->user_username,
                    'email' => $row->user_email,
                    'departemen' => $row->departemen_nama ?: '-',
                ],
                'kategori' => $row->kategori_id ? [
                    'id' => $row->kategori_id,
                    'kode' => $row->kategori_kode,
                    'nama' => $row->kategori_nama,
                    'nominal' => $nominal,
                ] : null,
                'perolehan_dana' => $nominal,
            ];
        });

        $responsePayload = [
            'status' => true,
            'message' => 'Data absensi berhasil diambil',
        ];

        if ($mode) {
            $responsePayload['periode'] = [
                'mode' => $mode,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ];
        }

        $responsePayload['data'] = $items;
        $responsePayload['pagination'] = [
            'total' => $paginated->total(),
            'per_page' => $paginated->perPage(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'next_page_url' => $paginated->nextPageUrl(),
            'prev_page_url' => $paginated->previousPageUrl(),
        ];

        return response()->json($responsePayload);
    }

    /**
     * Get Rekap Data Absensi Pengguna
     * Mendukung filter mode periode:
     * - mode=bulan_tahun (param: bulan, tahun)
     * - mode=rentang_tanggal (param: start_date, end_date)
     */
    public function rekap(Request $request)
    {
        $limit = (int) $request->get('limit', 15);
        $mode = $request->get('mode', 'bulan_tahun');

        $startDate = null;
        $endDate = null;
        $bulan = null;
        $tahun = null;

        if ($mode === 'rentang_tanggal' || ($request->filled('start_date') && $request->filled('end_date'))) {
            $mode = 'rentang_tanggal';
            $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        } else {
            $mode = 'bulan_tahun';
            $bulan = (int) $request->get('bulan', now()->month);
            $tahun = (int) $request->get('tahun', now()->year);
            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay()->format('Y-m-d');
            $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');
        }

        // Ambil semua kategori diurutkan dari selisih tertinggi atau kode
        $kategoriList = Kategori::orderBy('selisih', 'desc')->orderBy('id', 'asc')->get();

        // Query Pengguna (Users)
        $usersQuery = User::leftJoin('departemen', 'departemen.id', '=', 'users.departemen_id')
            ->select('users.id', 'users.kode', 'users.name', 'users.username', 'users.email', 'departemen.nama as departemen_nama');

        if ($request->filled('user_id')) {
            $usersQuery->where('users.id', $request->user_id);
        }
        if ($request->filled('kode_user')) {
            $usersQuery->where('users.kode', $request->kode_user);
        } elseif ($request->filled('kode')) {
            $usersQuery->where('users.kode', $request->kode);
        }

        if ($request->filled('departemen_id')) {
            $usersQuery->where('users.departemen_id', $request->departemen_id);
        }

        if ($request->filled('search')) {
            $term = trim((string) $request->search);
            $usersQuery->where(function ($q) use ($term) {
                $q->orWhere('users.name', 'LIKE', "%{$term}%");
                $q->orWhere('users.username', 'LIKE', "%{$term}%");
                $q->orWhere('users.kode', 'LIKE', "%{$term}%");
                $q->orWhere('users.email', 'LIKE', "%{$term}%");
            });
        }

        $usersQuery->orderBy('users.name', 'asc');
        $paginatedUsers = $usersQuery->paginate($limit);

        // Ambil semua absensi untuk user-user yang tampil di halaman ini dan rentang tanggal yang dipilih
        $userIds = collect($paginatedUsers->items())->pluck('id')->toArray();
        
        $absensiRecords = Absensi::whereIn('users_id', $userIds)
            ->whereBetween('tgl_absen', [$startDate, $endDate])
            ->get();

        $rekapResult = collect($paginatedUsers->items())->map(function ($user) use ($absensiRecords, $kategoriList) {
            $userAbsensi = $absensiRecords->where('users_id', $user->id);

            $rekapPerKategori = [];
            $totalPerolehanDana = 0;
            $totalMenitKeseluruhan = 0;

            foreach ($kategoriList as $kat) {
                // Cari records absensi milik user pada kategori ini
                $katRecords = $userAbsensi->where('kategori_id', $kat->id);
                $jumlah = $katRecords->count();
                $nominal = (float) $kat->nominal;
                $perolehanDana = $jumlah * $nominal;

                $totalPerolehanDana += $perolehanDana;

                $rekapPerKategori[] = [
                    'kategori_id' => $kat->id,
                    'kode' => $kat->kode,
                    'nama' => $kat->nama,
                    'nominal' => $nominal,
                    'jumlah' => $jumlah,
                    'perolehan_dana' => $perolehanDana,
                ];
            }

            // Hitung total jam keseluruhan dalam menit
            foreach ($userAbsensi as $abs) {
                $menit = (int) $abs->selisih;
                if ($menit <= 0 && $abs->pagi && $abs->sore) {
                    $menit = RealtimeAbsensi::selisihMenit($abs->tgl_absen, $abs->pagi, $abs->sore);
                }
                $totalMenitKeseluruhan += $menit;
            }

            return [
                'kode_user' => $user->kode ?: '-',
                'user' => [
                    'id' => $user->id,
                    'kode' => $user->kode ?: '-',
                    'kode_user' => $user->kode ?: '-',
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'departemen' => $user->departemen_nama ?: '-',
                ],
                'rekap_per_kategori' => $rekapPerKategori,
                'total_jam_keseluruhan' => [
                    'total_menit' => $totalMenitKeseluruhan,
                    'total_jam' => round($totalMenitKeseluruhan / 60, 2),
                    'format_teks' => RealtimeAbsensi::formatMenit($totalMenitKeseluruhan),
                ],
                'total_perolehan_dana' => $totalPerolehanDana,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Rekap absensi berhasil diambil',
            'periode' => [
                'mode' => $mode,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ],
            'data' => $rekapResult,
            'pagination' => [
                'total' => $paginatedUsers->total(),
                'per_page' => $paginatedUsers->perPage(),
                'current_page' => $paginatedUsers->currentPage(),
                'last_page' => $paginatedUsers->lastPage(),
                'next_page_url' => $paginatedUsers->nextPageUrl(),
                'prev_page_url' => $paginatedUsers->previousPageUrl(),
            ]
        ]);
    }

    /**
     * Helper / Generator Signature (Tanpa proteksi middleware untuk kemudahan testing)
     */
    public function signatureGeneratorHelper(Request $request)
    {
        $apiKey = $request->get('api_key');
        $secretKey = $request->get('secret_key');
        $method = strtoupper($request->get('method', 'GET'));
        $path = $request->get('path', 'api/client/v1/absensi');
        $timestamp = $request->get('timestamp', time());

        if (!$apiKey || !$secretKey) {
            return response()->json([
                'status' => false,
                'message' => 'Harap sertakan parameter query api_key dan secret_key.',
                'contoh_penggunaan' => url('/api/client/v1/signature-helper?api_key=KEY_ANDA&secret_key=SECRET_ANDA&method=GET&path=api/client/v1/absensi/rekap&timestamp=' . time()),
            ]);
        }

        $stringToSign = $method . ':' . $path . ':' . $timestamp;
        $stringToSignFallback = $apiKey . ':' . $timestamp;

        $signaturePrimary = hash_hmac('sha256', $stringToSign, $secretKey);
        $signatureFallback = hash_hmac('sha256', $stringToSignFallback, $secretKey);

        return response()->json([
            'status' => true,
            'message' => 'Signature berhasil digenerate',
            'params' => [
                'api_key' => $apiKey,
                'method' => $method,
                'path' => $path,
                'timestamp' => (string) $timestamp,
            ],
            'string_to_sign_primary' => $stringToSign,
            'signature_primary' => $signaturePrimary,
            'string_to_sign_fallback' => $stringToSignFallback,
            'signature_fallback' => $signatureFallback,
            'headers_to_send' => [
                'X-Api-Key' => $apiKey,
                'X-Timestamp' => (string) $timestamp,
                'X-Signature' => $signaturePrimary,
            ]
        ]);
    }
}
