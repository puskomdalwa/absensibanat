<?php

namespace App\Http\Controllers;

use App\Exports\UserAbsensiDetailExport;
use App\Http\Services\RealtimeAbsensi;
use App\Models\Absensi;
use App\Models\Departemen;
use App\Models\Device;
use App\Models\Kategori;
use App\Models\User;
use App\Models\Verify;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LaporanController extends Controller
{
    public function userIndex()
    {
        $user = Auth::user();
        $kategori = $this->kategoriOptions();
        $canFilterKategori = $this->isStaff($user);

        return view('home.laporan.index', compact('user', 'kategori', 'canFilterKategori'));
    }

    public function userData(Request $request)
    {
        $user = Auth::user();

        $data = $this->queryLaporan($request)
            ->where('absensi.users_id', $user->id);

        if ($this->isStaff($user) && $request->kategori_id && $request->kategori_id !== '*') {
            $data->where('absensi.kategori_id', $request->kategori_id);
        }

        return $this->datatable($data);
    }

    public function adminIndex()
    {
        $kategori = $this->kategoriOptions();
        $departemen = Departemen::orderBy('nama')->get();

        return view('admin.laporan.index', compact('kategori', 'departemen'));
    }

    public function adminData(Request $request)
    {
        return response()->json($this->adminRekapBulanan($request));
    }

    private function adminRekapBulanan(Request $request): array
    {
        $bulan = $request->month ?: now()->format('Y-m');
        $tanggalAwal = Carbon::createFromFormat('Y-m-d', $bulan . '-01')->startOfDay();
        $tanggalAkhir = $tanggalAwal->copy()->endOfMonth();
        $kategori = Kategori::orderBy('kode')->get();

        $rekap = Absensi::join('kategori', 'kategori.id', '=', 'absensi.kategori_id')
            ->join('users', 'users.id', '=', 'absensi.users_id')
            ->select(
                'absensi.tgl_absen',
                'absensi.kategori_id',
                DB::raw('COUNT(absensi.id) as total_absensi'),
                DB::raw('SUM(kategori.nominal) as total_nominal')
            )
            ->whereBetween('absensi.tgl_absen', [$tanggalAwal->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->when($request->kategori_id && $request->kategori_id !== '*', function ($query) use ($request) {
                $query->where('absensi.kategori_id', $request->kategori_id);
            })
            ->when($request->departemen_id && $request->departemen_id !== '*', function ($query) use ($request) {
                $query->where('users.departemen_id', $request->departemen_id);
            })
            ->groupBy('absensi.tgl_absen', 'absensi.kategori_id')
            ->get()
            ->groupBy('tgl_absen');

        $rows = [];
        $nomor = 1;
        for ($date = $tanggalAwal->copy(); $date->lte($tanggalAkhir); $date->addDay()) {
            $tanggal = $date->format('Y-m-d');
            $values = [];

            foreach ($kategori as $item) {
                $itemRekap = optional($rekap->get($tanggal))->firstWhere('kategori_id', $item->id);
                $values[$item->id] = [
                    'amount' => $itemRekap ? (float) $itemRekap->total_nominal : null,
                    'count' => $itemRekap ? (int) $itemRekap->total_absensi : 0,
                ];
            }

            $rows[] = [
                'no' => $nomor++,
                'tanggal' => $date->format('d/m/Y'),
                'values' => $values,
            ];
        }

        return [
            'title' => 'PEMASUKAN TUNAI BULAN ' . $this->namaBulan($tanggalAwal->month) . ' ' . $tanggalAwal->year,
            'categories' => $kategori->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'kode' => $item->kode,
                ];
            })->values(),
            'rows' => $rows,
        ];
    }

    public function adminUserDetail(User $user)
    {
        $kategori = $this->kategoriOptions();
        $device = Device::orderBy('name')->get();
        $verify = Verify::orderBy('name')->get();
        $tahunAbsensi = Absensi::where('users_id', $user->id)
            ->selectRaw('YEAR(tgl_absen) as tahun')
            ->whereNotNull('tgl_absen')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->filter()
            ->push(now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        return view('admin.user.detail-absensi', compact('user', 'kategori', 'device', 'verify', 'tahunAbsensi'));
    }

    public function adminUserDetailSummary(User $user, Request $request)
    {
        $report = $this->userAbsensiReport($user, $request);

        return response()->json([
            'period_label' => $report['period_label'],
            'summary' => $report['summary'],
            'categories' => $report['categories'],
            'daily' => $report['daily'],
        ]);
    }

    public function adminUserDetailData(User $user, Request $request)
    {
        return $this->datatable(
            $this->queryLaporan($request)->where('absensi.users_id', $user->id),
            $request->user()->isAdmin()
        );
    }

    public function adminUserDetailExportExcel(User $user, Request $request)
    {
        $report = $this->userAbsensiReport($user, $request);

        return Excel::download(
            new UserAbsensiDetailExport($report),
            $this->userAbsensiExportFilename($user, $request, 'xlsx')
        );
    }

    public function adminUserDetailExportPdf(User $user, Request $request)
    {
        $report = $this->userAbsensiReport($user, $request);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('admin.user.exports.detail-absensi-pdf', compact('report'))->render());
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $this->userAbsensiExportFilename($user, $request, 'pdf') . '"',
        ]);
    }

    private function queryLaporan(Request $request)
    {
        $query = Absensi::join('users', 'users.id', '=', 'absensi.users_id')
            ->leftJoin('kategori', 'kategori.id', '=', 'absensi.kategori_id')
            ->select(
                'absensi.*',
                'users.name as user_name',
                'users.username as user_username',
                'kategori.nama as kategori_nama',
                'kategori.kode as kategori_kode',
                'kategori.nominal as kategori_nominal'
            );

        return $this->applyLaporanFilters($query, $request);
    }

    private function datatable($data, bool $withAction = false)
    {
        $search = request('search.value');

        return DataTables::of($data)
            ->filter(function ($query) use ($search) {
                if (!$search) {
                    return;
                }

                $query->where(function ($query) use ($search) {
                    $query->orWhere('users.name', 'LIKE', "%$search%");
                    $query->orWhere('users.username', 'LIKE', "%$search%");
                    $query->orWhere('absensi.tgl_absen', 'LIKE', "%$search%");
                    $query->orWhere('absensi.pagi', 'LIKE', "%$search%");
                    $query->orWhere('absensi.sore', 'LIKE', "%$search%");
                    $query->orWhere('kategori.nama', 'LIKE', "%$search%");
                    $query->orWhere('kategori.kode', 'LIKE', "%$search%");
                    $query->orWhere('absensi.keterangan', 'LIKE', "%$search%");
                });
            })
            ->editColumn('kategori_nama', function ($row) {
                return $row->kategori_nama ?: '-';
            })
            ->editColumn('kategori_nominal', function ($row) {
                return $row->kategori_nominal !== null ? $this->formatRupiah((float) $row->kategori_nominal) : '-';
            })
            ->addColumn('durasi_efektif', function ($row) {
                $menit = (int) ($row->selisih ?? 0);
                if ($menit <= 0) {
                    $menit = RealtimeAbsensi::selisihMenit($row->tgl_absen, $row->pagi, $row->sore);
                }

                return RealtimeAbsensi::formatMenit($menit);
            })
            ->addColumn('action', function ($row) use ($withAction) {
                if (! $withAction) {
                    return '';
                }

                return '
                    <button class="btn btn-sm btn-primary Btnedit"
                        data-id="' . $row->id . '"
                        data-users_id="' . $row->users_id . '"
                        data-user_name="' . e($row->user_name) . '"
                        data-tgl="' . $row->tgl_absen . '"
                        data-pagi="' . $row->pagi . '"
                        data-sore="' . $row->sore . '"
                        data-device_id="' . $row->device_id . '"
                        data-verify_id="' . $row->verify_id . '"
                        data-latitude="' . e($row->latitude) . '"
                        data-longitude="' . e($row->longitude) . '"
                        data-ket="' . e($row->keterangan) . '"
                    >Edit</button>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    private function queryUserAbsensiRows(User $user, Request $request)
    {
        $query = DB::table('absensi')
            ->leftJoin('kategori', 'kategori.id', '=', 'absensi.kategori_id')
            ->select(
                'absensi.id',
                'absensi.tgl_absen',
                'absensi.pagi',
                'absensi.sore',
                'absensi.selisih',
                'absensi.keterangan',
                'absensi.kategori_id',
                'kategori.nama as kategori_nama',
                'kategori.kode as kategori_kode',
                'kategori.nominal as kategori_nominal'
            )
            ->where('absensi.users_id', $user->id);

        return $this->applyLaporanFilters($query, $request)
            ->orderBy('absensi.tgl_absen');
    }

    private function applyLaporanFilters($query, Request $request)
    {
        $filterType = $request->input('filter_type');
        $startDate = $request->input('start_date', $request->input('startDate'));
        $endDate = $request->input('end_date', $request->input('endDate'));

        if ($filterType === 'semua') {
            // Tidak membatasi tanggal.
        } elseif ($filterType === 'rentang_tanggal' || (!$filterType && ($startDate || $endDate))) {
            if ($startDate && $endDate) {
                $query->whereBetween('absensi.tgl_absen', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->where('absensi.tgl_absen', '>=', $startDate);
            } elseif ($endDate) {
                $query->where('absensi.tgl_absen', '<=', $endDate);
            }
        } elseif ($filterType === 'bulan_tahun' || (!$filterType && ($request->has('bulan') || $request->has('tahun')))) {
            $bulan = $request->input('bulan', '*') ?: '*';
            $tahun = $request->input('tahun', '*') ?: '*';

            if ($bulan !== '*') {
                $query->whereMonth('absensi.tgl_absen', $bulan);
            }

            if ($tahun !== '*') {
                $query->whereYear('absensi.tgl_absen', $tahun);
            }
        }

        $kategoriId = $request->input('kategori_id', '*') ?: '*';
        if ($kategoriId !== '*') {
            $query->where('absensi.kategori_id', $kategoriId);
        }

        return $query;
    }

    private function rowMenitEfektif($row): int
    {
        $menit = (int) ($row->selisih ?? 0);

        if ($menit <= 0) {
            $menit = RealtimeAbsensi::selisihMenit($row->tgl_absen, $row->pagi, $row->sore);
        }

        return $menit;
    }

    private function userAbsensiReport(User $user, Request $request): array
    {
        $rows = $this->queryUserAbsensiRows($user, $request)->get();
        $kategori = $this->kategoriOptions();

        $summaryByCategory = $kategori->mapWithKeys(function ($item) {
            return [
                $item->id => [
                    'id' => $item->id,
                    'kode' => $item->kode,
                    'nama' => $item->nama,
                    'nominal_per_absensi' => (float) $item->nominal,
                    'nominal_per_absensi_formatted' => $this->formatRupiah((float) $item->nominal),
                    'total_absensi' => 0,
                    'total_nominal' => 0,
                    'total_nominal_formatted' => $this->formatRupiah(0),
                    'total_menit' => 0,
                    'durasi_teks' => '-',
                ],
            ];
        })->all();

        $totalMenit = 0;
        $totalNominal = 0;
        $daily = [];
        $details = [];

        foreach ($rows as $index => $row) {
            $menit = $this->rowMenitEfektif($row);
            $nominal = (float) ($row->kategori_nominal ?? 0);
            $kategoriKey = $row->kategori_id ?: 'tanpa_kategori';

            if (!isset($summaryByCategory[$kategoriKey])) {
                $summaryByCategory[$kategoriKey] = [
                    'id' => $row->kategori_id,
                    'kode' => $row->kategori_kode ?: '-',
                    'nama' => $row->kategori_nama ?: 'Tanpa Kategori',
                    'nominal_per_absensi' => $nominal,
                    'nominal_per_absensi_formatted' => $this->formatRupiah($nominal),
                    'total_absensi' => 0,
                    'total_nominal' => 0,
                    'total_nominal_formatted' => $this->formatRupiah(0),
                    'total_menit' => 0,
                    'durasi_teks' => '-',
                ];
            }

            $summaryByCategory[$kategoriKey]['total_absensi']++;
            $summaryByCategory[$kategoriKey]['total_nominal'] += $nominal;
            $summaryByCategory[$kategoriKey]['total_menit'] += $menit;

            if (!isset($daily[$row->tgl_absen])) {
                $daily[$row->tgl_absen] = [
                    'tanggal' => $row->tgl_absen,
                    'tanggal_label' => Carbon::parse($row->tgl_absen)->format('d/m'),
                    'total_absensi' => 0,
                    'total_nominal' => 0,
                    'total_menit' => 0,
                ];
            }

            $daily[$row->tgl_absen]['total_absensi']++;
            $daily[$row->tgl_absen]['total_nominal'] += $nominal;
            $daily[$row->tgl_absen]['total_menit'] += $menit;

            $totalMenit += $menit;
            $totalNominal += $nominal;

            $details[] = [
                'no' => $index + 1,
                'tanggal' => $row->tgl_absen,
                'tanggal_formatted' => Carbon::parse($row->tgl_absen)->format('d/m/Y'),
                'jam_datang' => $row->pagi ?: '-',
                'jam_pulang' => $row->sore ?: '-',
                'kategori' => trim(($row->kategori_kode ?: '-') . ' - ' . ($row->kategori_nama ?: 'Tanpa Kategori')),
                'durasi_menit' => $menit,
                'durasi_teks' => RealtimeAbsensi::formatMenit($menit),
                'nominal' => $nominal,
                'nominal_formatted' => $this->formatRupiah($nominal),
                'keterangan' => $row->keterangan ?: '-',
            ];
        }

        ksort($daily);

        $categoryRows = collect($summaryByCategory)
            ->filter(function ($item) {
                return $item['total_absensi'] > 0;
            })
            ->map(function ($item) {
                $item['total_nominal_formatted'] = $this->formatRupiah((float) $item['total_nominal']);
                $item['durasi_teks'] = RealtimeAbsensi::formatMenit((int) $item['total_menit']);
                return $item;
            })
            ->values();

        $totalAbsensi = $rows->count();

        return [
            'user' => $user,
            'period_label' => $this->laporanPeriodLabel($request),
            'summary' => [
                'total_absensi' => $totalAbsensi,
                'total_nominal' => $totalNominal,
                'total_nominal_formatted' => $this->formatRupiah($totalNominal),
                'total_durasi_menit' => $totalMenit,
                'total_durasi_teks' => RealtimeAbsensi::formatMenit($totalMenit),
                'rata_nominal' => $totalAbsensi > 0 ? $totalNominal / $totalAbsensi : 0,
                'rata_nominal_formatted' => $this->formatRupiah($totalAbsensi > 0 ? $totalNominal / $totalAbsensi : 0),
            ],
            'categories' => $categoryRows,
            'daily' => array_values($daily),
            'details' => $details,
        ];
    }

    private function userAbsensiExportFilename(User $user, Request $request, string $extension): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $user->username ?: $user->name), '-'));

        if ($slug === '') {
            $slug = 'user-' . $user->id;
        }

        return 'absensi-' . $slug . '-' . $this->laporanPeriodFilename($request) . '.' . $extension;
    }

    private function laporanPeriodFilename(Request $request): string
    {
        $filterType = $request->input('filter_type');
        $startDate = $request->input('start_date', $request->input('startDate'));
        $endDate = $request->input('end_date', $request->input('endDate'));

        if ($filterType === 'semua') {
            return 'semua-periode';
        }

        if ($filterType === 'rentang_tanggal' || (!$filterType && ($startDate || $endDate))) {
            return ($startDate ?: 'awal') . '-sampai-' . ($endDate ?: 'akhir');
        }

        $bulan = $request->input('bulan', '*') ?: '*';
        $tahun = $request->input('tahun', '*') ?: '*';

        return ($tahun !== '*' ? $tahun : 'semua-tahun')
            . '-'
            . ($bulan !== '*' ? str_pad($bulan, 2, '0', STR_PAD_LEFT) : 'semua-bulan');
    }

    private function kategoriOptions()
    {
        return Kategori::orderBy('selisih', 'desc')->orderBy('id')->get();
    }

    private function laporanPeriodLabel(Request $request): string
    {
        $filterType = $request->input('filter_type');
        $startDate = $request->input('start_date', $request->input('startDate'));
        $endDate = $request->input('end_date', $request->input('endDate'));

        if ($filterType === 'semua') {
            return 'Semua periode';
        }

        if ($filterType === 'rentang_tanggal' || (!$filterType && ($startDate || $endDate))) {
            if ($startDate && $endDate) {
                return Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
            }

            if ($startDate) {
                return 'Mulai ' . Carbon::parse($startDate)->format('d/m/Y');
            }

            if ($endDate) {
                return 'Sampai ' . Carbon::parse($endDate)->format('d/m/Y');
            }
        }

        $bulan = $request->input('bulan', '*') ?: '*';
        $tahun = $request->input('tahun', '*') ?: '*';

        if ($bulan !== '*' && $tahun !== '*') {
            return $this->namaBulan((int) $bulan) . ' ' . $tahun;
        }

        if ($bulan !== '*') {
            return $this->namaBulan((int) $bulan) . ' semua tahun';
        }

        if ($tahun !== '*') {
            return 'Semua bulan ' . $tahun;
        }

        return 'Semua periode';
    }

    private function formatRupiah(float $value): string
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    private function isStaff(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        $role = optional($user->role)->akses;
        $departemen = optional($user->departemen)->nama;

        return strtolower((string) $role) === 'staff' || strtolower((string) $departemen) === 'staff';
    }

    private function namaBulan(int $bulan): string
    {
        $nama = [
            1 => 'JANUARI',
            2 => 'FEBRUARI',
            3 => 'MARET',
            4 => 'APRIL',
            5 => 'MEI',
            6 => 'JUNI',
            7 => 'JULI',
            8 => 'AGUSTUS',
            9 => 'SEPTEMBER',
            10 => 'OKTOBER',
            11 => 'NOVEMBER',
            12 => 'DESEMBER',
        ];

        return $nama[$bulan] ?? '';
    }
}
