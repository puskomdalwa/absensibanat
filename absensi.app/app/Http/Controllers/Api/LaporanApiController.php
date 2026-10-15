<?php

namespace App\Http\Controllers\Api;

use App\Exports\LaporanRekapBulananExport;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanApiController extends Controller
{
    /**
     * Dapatkan opsi filter (Departemen & Kategori)
     */
    public function getFilterOptions()
    {
        $departemen = Departemen::orderBy('nama')->get(['id', 'nama']);
        $kategori = Kategori::orderBy('kode')->get(['id', 'nama', 'kode', 'nominal']);

        return response()->json([
            'status' => true,
            'message' => 'Berhasil memuat filter options',
            'data' => [
                'departemen' => $departemen,
                'kategori' => $kategori,
            ]
        ]);
    }

    /**
     * Dapatkan data rekapitulasi bulanan persis seperti /admin/laporan
     */
    public function getRekapBulanan(Request $request)
    {
        $data = $this->buildRekapData($request);

        return response()->json([
            'status' => true,
            'message' => 'Berhasil memuat rekapitulasi presensi',
            'data' => $data,
        ]);
    }

    /**
     * Export rekap bulanan ke format Excel (.xlsx) yang rapi & elegan
     */
    public function exportExcel(Request $request)
    {
        $data = $this->buildRekapData($request);
        $bulanStr = $request->month ?: now()->format('Y-m');
        $filename = 'Laporan_Rekap_Presensi_Banat_' . str_replace('-', '_', $bulanStr) . '.xlsx';

        // Filter metadata descriptions
        $depNama = 'Semua Departemen';
        if ($request->departemen_id && $request->departemen_id !== '*') {
            $dep = Departemen::find($request->departemen_id);
            if ($dep) $depNama = $dep->nama;
        }

        $katNama = 'Semua Kategori';
        if ($request->kategori_id && $request->kategori_id !== '*') {
            $kat = Kategori::find($request->kategori_id);
            if ($kat) $katNama = $kat->kode . ' - ' . $kat->nama;
        }

        $filters = [
            'departemen_nama' => $depNama,
            'kategori_nama' => $katNama,
        ];

        $export = new LaporanRekapBulananExport($data, $data['category_models'], $filters, $data['show_nominal'] ?? Setting::isNominalVisible());

        // Jika klien meminta base64 (untuk web / in-memory download)
        if ($request->input('format') === 'base64') {
            $rawExcel = Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);
            return response()->json([
                'status' => true,
                'filename' => $filename,
                'base64' => base64_encode($rawExcel),
                'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        return Excel::download($export, $filename);
    }

    /**
     * Helper perhitungan data rekapitulasi
     */
    private function buildRekapData(Request $request): array
    {
        $bulan = $request->month ?: now()->format('Y-m');
        try {
            $tanggalAwal = Carbon::createFromFormat('Y-m-d', $bulan . '-01')->startOfDay();
        } catch (\Exception $e) {
            $tanggalAwal = now()->startOfMonth();
            $bulan = $tanggalAwal->format('Y-m');
        }
        $tanggalAkhir = $tanggalAwal->copy()->endOfMonth();

        $kategoriQuery = Kategori::orderBy('kode');
        if ($request->kategori_id && $request->kategori_id !== '*') {
            $kategoriQuery->where('id', $request->kategori_id);
        }
        $kategori = $kategoriQuery->get();

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
        $grandTotalPresensi = 0;
        $grandTotalNominal = 0;

        $categoryTotals = [];
        foreach ($kategori as $item) {
            $categoryTotals[$item->id] = [
                'count' => 0,
                'amount' => 0,
                'nama' => $item->nama,
                'kode' => $item->kode,
            ];
        }

        for ($date = $tanggalAwal->copy(); $date->lte($tanggalAkhir); $date->addDay()) {
            $tanggal = $date->format('Y-m-d');
            $values = [];
            $dailyTotalCount = 0;
            $dailyTotalAmount = 0;

            foreach ($kategori as $item) {
                $itemRekap = optional($rekap->get($tanggal))->firstWhere('kategori_id', $item->id);
                $cnt = $itemRekap ? (int) $itemRekap->total_absensi : 0;
                $amt = $itemRekap ? (float) $itemRekap->total_nominal : null;

                $values[$item->id] = [
                    'amount' => $amt,
                    'amount_formatted' => $amt !== null ? 'Rp ' . number_format($amt, 0, ',', '.') : '-',
                    'count' => $cnt,
                ];

                $dailyTotalCount += $cnt;
                if ($amt !== null) {
                    $dailyTotalAmount += $amt;
                }

                $categoryTotals[$item->id]['count'] += $cnt;
                if ($amt !== null) {
                    $categoryTotals[$item->id]['amount'] += $amt;
                }
            }

            $rows[] = [
                'no' => $nomor++,
                'tanggal' => $date->format('d/m/Y'),
                'tanggal_raw' => $tanggal,
                'total_harian' => $dailyTotalCount,
                'nominal_harian' => $dailyTotalAmount,
                'nominal_harian_formatted' => 'Rp ' . number_format($dailyTotalAmount, 0, ',', '.'),
                'values' => $values,
            ];

            $grandTotalPresensi += $dailyTotalCount;
            $grandTotalNominal += $dailyTotalAmount;
        }

        $showNominal = Setting::isNominalVisible(Auth::user());

        foreach ($categoryTotals as $id => $tot) {
            $categoryTotals[$id]['amount_formatted'] = $showNominal ? 'Rp ' . number_format($tot['amount'], 0, ',', '.') : '-';
        }

        $namaBulanIndonesia = [
            1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
            5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
            9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
        ];

        $bulanNama = $namaBulanIndonesia[$tanggalAwal->month] ?? strtoupper($tanggalAwal->format('F'));
        $title = $showNominal
            ? 'PEMASUKAN TUNAI BULAN ' . $bulanNama . ' ' . $tanggalAwal->year
            : 'REKAPITULASI PRESENSI BULAN ' . $bulanNama . ' ' . $tanggalAwal->year;
        $periodLabel = ucfirst(strtolower($bulanNama)) . ' ' . $tanggalAwal->year;

        return [
            'title' => $title,
            'period_label' => $periodLabel,
            'total_presensi' => $grandTotalPresensi,
            'total_nominal' => $showNominal ? $grandTotalNominal : 0,
            'total_nominal_formatted' => $showNominal ? 'Rp ' . number_format($grandTotalNominal, 0, ',', '.') : '-',
            'show_nominal' => $showNominal,
            'categories' => $kategori->map(function ($item) use ($showNominal) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'kode' => $item->kode,
                    'nominal' => $showNominal ? (float)$item->nominal : 0,
                ];
            })->values(),
            'category_models' => $kategori,
            'category_totals' => $categoryTotals,
            'rows' => $rows,
        ];
    }
}
