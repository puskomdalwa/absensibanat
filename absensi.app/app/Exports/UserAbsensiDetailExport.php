<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UserAbsensiDetailExport implements FromArray, ShouldAutoSize
{
    private $report;
    private $showNominal;

    public function __construct(array $report, ?bool $showNominal = null)
    {
        $this->report = $report;
        $this->showNominal = $showNominal ?? ($report['show_nominal'] ?? true);
    }

    public function array(): array
    {
        $user = $this->report['user'];
        $rows = [
            ['LAPORAN DETAIL ABSENSI USER'],
            ['Nama', $user->name],
            ['Username', $user->username],
            ['Role', optional($user->role)->akses ?? '-'],
            ['Departemen', optional($user->departemen)->nama ?? '-'],
            ['Periode', $this->report['period_label']],
            ['Total Absensi', $this->report['summary']['total_absensi']],
        ];

        if ($this->showNominal) {
            $rows[] = ['Total Nominal', $this->report['summary']['total_nominal_formatted']];
        }

        $rows[] = ['Total Durasi', $this->report['summary']['total_durasi_teks']];

        if ($this->showNominal) {
            $rows[] = ['Rata-rata Per Absensi', $this->report['summary']['rata_nominal_formatted']];
        }

        $rows[] = [];
        $rows[] = ['REKAP KATEGORI'];

        if ($this->showNominal) {
            $rows[] = ['Kategori', 'Nominal Satuan', 'Jumlah Absensi', 'Total Durasi', 'Total Nominal'];
            foreach ($this->report['categories'] as $category) {
                $rows[] = [
                    $category['kode'] . ' - ' . $category['nama'],
                    $category['nominal_per_absensi_formatted'],
                    $category['total_absensi'],
                    $category['durasi_teks'],
                    $category['total_nominal_formatted'],
                ];
            }
        } else {
            $rows[] = ['Kategori', 'Jumlah Absensi', 'Total Durasi'];
            foreach ($this->report['categories'] as $category) {
                $rows[] = [
                    $category['kode'] . ' - ' . $category['nama'],
                    $category['total_absensi'],
                    $category['durasi_teks'],
                ];
            }
        }

        $rows[] = [];
        $rows[] = ['DETAIL ABSENSI'];

        if ($this->showNominal) {
            $rows[] = ['No', 'Tanggal', 'Jam Datang', 'Jam Pulang', 'Kategori', 'Durasi Efektif', 'Nominal', 'Keterangan'];
            foreach ($this->report['details'] as $detail) {
                $rows[] = [
                    $detail['no'],
                    $detail['tanggal_formatted'],
                    $detail['jam_datang'],
                    $detail['jam_pulang'],
                    $detail['kategori'],
                    $detail['durasi_teks'],
                    $detail['nominal_formatted'],
                    $detail['keterangan'],
                ];
            }
        } else {
            $rows[] = ['No', 'Tanggal', 'Jam Datang', 'Jam Pulang', 'Kategori', 'Durasi Efektif', 'Keterangan'];
            foreach ($this->report['details'] as $detail) {
                $rows[] = [
                    $detail['no'],
                    $detail['tanggal_formatted'],
                    $detail['jam_datang'],
                    $detail['jam_pulang'],
                    $detail['kategori'],
                    $detail['durasi_teks'],
                    $detail['keterangan'],
                ];
            }
        }

        return $rows;
    }
}
