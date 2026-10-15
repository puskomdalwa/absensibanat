<?php

namespace App\Exports;

use App\Models\Setting;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanRekapBulananExport implements FromArray, ShouldAutoSize, WithStyles
{
    private $rekap;
    private $categories;
    private $filters;
    private $showNominal;
    private $headerRowCount = 0;
    private $tableStartRow = 0;
    private $totalRowIndex = 0;

    public function __construct(array $rekap, $categories, array $filters, ?bool $showNominal = null)
    {
        $this->rekap = $rekap;
        $this->categories = $categories;
        $this->filters = $filters;
        $this->showNominal = $showNominal ?? ($rekap['show_nominal'] ?? Setting::isNominalVisible());
    }

    public function array(): array
    {
        $rows = [];

        // 1. Title Banner
        $rows[] = ['REKAPITULASI PRESENSI ' . ($this->showNominal ? '& PEMASUKAN ' : '') . 'BANAT UII DALWA'];
        $rows[] = [$this->rekap['title'] ?? 'REKAP BULANAN'];
        $rows[] = [];

        // 2. Metadata / Filter info
        $rows[] = ['Periode Bulan', $this->rekap['period_label'] ?? '-'];
        $rows[] = ['Departemen', $this->filters['departemen_nama'] ?? 'Semua Departemen'];
        $rows[] = ['Kategori', $this->filters['kategori_nama'] ?? 'Semua Kategori'];
        $rows[] = ['Waktu Unduh', now()->translatedFormat('d F Y H:i:s') . ' WIB'];
        $rows[] = ['Total Kehadiran', ($this->rekap['total_presensi'] ?? 0) . ' Santri/Staf'];
        if ($this->showNominal) {
            $rows[] = ['Total Nominal', $this->rekap['total_nominal_formatted'] ?? 'Rp 0'];
        }
        $rows[] = [];

        // 3. Table Column Headers
        $tableHeaders = ['No', 'Tanggal'];
        foreach ($this->categories as $cat) {
            $tableHeaders[] = $cat->kode . ' (Jml)';
            if ($this->showNominal) {
                $tableHeaders[] = $cat->kode . ' (Rp)';
            }
        }
        $tableHeaders[] = 'Total Kehadiran';
        if ($this->showNominal) {
            $tableHeaders[] = 'Total Nominal (Rp)';
        }

        $rows[] = $tableHeaders;
        $this->tableStartRow = count($rows);

        // 4. Data Rows
        $categoryTotals = [];
        foreach ($this->categories as $cat) {
            $categoryTotals[$cat->id] = ['count' => 0, 'amount' => 0];
        }
        $grandTotalCount = 0;
        $grandTotalAmount = 0;

        foreach ($this->rekap['rows'] as $r) {
            $row = [
                $r['no'],
                $r['tanggal'],
            ];

            $dailyTotalCount = 0;
            $dailyTotalAmount = 0;

            foreach ($this->categories as $cat) {
                $val = $r['values'][$cat->id] ?? null;
                $count = $val ? (int)$val['count'] : 0;
                $amount = $val && $val['amount'] !== null ? (float)$val['amount'] : 0;

                $row[] = $count > 0 ? $count : '-';
                if ($this->showNominal) {
                    $row[] = $amount > 0 ? number_format($amount, 0, ',', '.') : '-';
                }

                $dailyTotalCount += $count;
                $dailyTotalAmount += $amount;

                $categoryTotals[$cat->id]['count'] += $count;
                $categoryTotals[$cat->id]['amount'] += $amount;
            }

            $row[] = $dailyTotalCount > 0 ? $dailyTotalCount : '-';
            if ($this->showNominal) {
                $row[] = $dailyTotalAmount > 0 ? number_format($dailyTotalAmount, 0, ',', '.') : '-';
            }

            $grandTotalCount += $dailyTotalCount;
            $grandTotalAmount += $dailyTotalAmount;

            $rows[] = $row;
        }

        // 5. Total Footer Row
        $footerRow = ['TOTAL', 'SEMUA HARI'];
        foreach ($this->categories as $cat) {
            $tot = $categoryTotals[$cat->id];
            $footerRow[] = $tot['count'] > 0 ? $tot['count'] : 0;
            if ($this->showNominal) {
                $footerRow[] = $tot['amount'] > 0 ? number_format($tot['amount'], 0, ',', '.') : '0';
            }
        }
        $footerRow[] = $grandTotalCount;
        if ($this->showNominal) {
            $footerRow[] = number_format($grandTotalAmount, 0, ',', '.');
        }

        $rows[] = $footerRow;
        $this->totalRowIndex = count($rows);

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $colsPerCat = $this->showNominal ? 2 : 1;
        $extraCols = $this->showNominal ? 2 : 1;
        $totalCols = 2 + (count($this->categories) * $colsPerCat) + $extraCols;
        $lastCol = $this->getColumnLetter($totalCols);

        // Title styling
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");

        $styles = [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2D283E'] // Banat Plum
                ],
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E05275'] // Banat Rose
                ],
            ],
            // Table Header Row
            $this->tableStartRow => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '8F2D56'] // Rose Dark
                ],
            ],
            // Total Footer Row
            $this->totalRowIndex => [
                'font' => ['bold' => true, 'color' => ['rgb' => '2D283E']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FCE8ED'] // Light Pearl Rose
                ],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E05275']],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => 'E05275']],
                ],
            ],
        ];

        // Format borders for the whole table
        $sheet->getStyle("A{$this->tableStartRow}:{$lastCol}{$this->totalRowIndex}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()
            ->setRGB('D1D5DB');

        return $styles;
    }

    private function getColumnLetter($index)
    {
        $letter = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index = (int)(($index - $mod) / 26);
        }
        return $letter ?: 'A';
    }
}
