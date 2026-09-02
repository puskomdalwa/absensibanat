<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 22px 24px 34px;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #2f2b3d;
            font-size: 10.5px;
            line-height: 1.35;
            background: #ffffff;
        }

        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -20px;
            color: #6f6b7d;
            font-size: 9px;
            text-align: right;
            border-top: 1px solid #dbdade;
            padding-top: 7px;
        }

        .header {
            background: #2f2b3d;
            color: #ffffff;
            padding: 18px 20px;
            border-radius: 8px;
        }

        .header h1 {
            font-size: 20px;
            margin: 0 0 5px;
            letter-spacing: .2px;
        }

        .header .subtitle {
            color: #d6d4f8;
            font-size: 11px;
        }

        .accent {
            height: 5px;
            background: #7367f0;
            margin: 0 14px 16px;
            border-radius: 0 0 5px 5px;
        }

        .meta-table,
        .summary-table,
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table {
            margin-bottom: 14px;
        }

        .meta-table td {
            padding: 4px 0;
            border: none;
        }

        .meta-label {
            width: 88px;
            color: #6f6b7d;
            font-weight: bold;
        }

        .meta-value {
            font-weight: bold;
        }

        .summary-table {
            margin: 12px 0 16px;
            border-spacing: 0;
            border-collapse: separate;
        }

        .summary-table td {
            width: 25%;
            border: 1px solid #dbdade;
            padding: 10px 12px;
            background: #f8f7fa;
        }

        .summary-table td + td {
            border-left: none;
        }

        .summary-label {
            color: #6f6b7d;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #2f2b3d;
        }

        .section-title {
            margin: 16px 0 7px;
            padding: 8px 10px;
            background: #7367f0;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px 6px 0 0;
        }

        .data-table {
            margin-bottom: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #dbdade;
            padding: 6px;
            vertical-align: top;
        }

        .data-table th {
            background: #2f2b3d;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
        }

        .data-table tbody tr:nth-child(even) td {
            background: #fafafc;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .nowrap {
            white-space: nowrap;
        }

        .muted {
            color: #6f6b7d;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            background: #edeafd;
            color: #7367f0;
            font-weight: bold;
            font-size: 9.5px;
        }
    </style>
</head>
<body>
    <div class="footer">
        Laporan Detail Absensi | {{ $report['user']->username }} | {{ $report['period_label'] }}
    </div>

    <div class="header">
        <h1>Laporan Detail Absensi User</h1>
        <div class="subtitle">
            {{ $report['user']->name }} | {{ $report['period_label'] }} | Dicetak {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
    <div class="accent"></div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Nama</td>
            <td class="meta-value">{{ $report['user']->name }}</td>
            <td class="meta-label">Role</td>
            <td class="meta-value">{{ optional($report['user']->role)->akses ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Username</td>
            <td class="meta-value">{{ $report['user']->username }}</td>
            <td class="meta-label">Departemen</td>
            <td class="meta-value">{{ optional($report['user']->departemen)->nama ?? '-' }}</td>
        </tr>
    </table>

    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-label">Total Absensi</div>
                <div class="summary-value">{{ number_format($report['summary']['total_absensi'], 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="summary-label">Total Nominal</div>
                <div class="summary-value">{{ $report['summary']['total_nominal_formatted'] }}</div>
            </td>
            <td>
                <div class="summary-label">Total Durasi</div>
                <div class="summary-value">{{ $report['summary']['total_durasi_teks'] }}</div>
            </td>
            <td>
                <div class="summary-label">Rata-rata</div>
                <div class="summary-value">{{ $report['summary']['rata_nominal_formatted'] }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Rekap Kategori</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Kategori</th>
                <th class="right">Nominal Satuan</th>
                <th class="right">Jumlah Absensi</th>
                <th class="right">Total Durasi</th>
                <th class="right">Total Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report['categories'] as $category)
                <tr>
                    <td><span class="badge">{{ $category['kode'] }}</span> {{ $category['nama'] }}</td>
                    <td class="right nowrap">{{ $category['nominal_per_absensi_formatted'] }}</td>
                    <td class="right">{{ number_format($category['total_absensi'], 0, ',', '.') }}</td>
                    <td class="right nowrap">{{ $category['durasi_teks'] }}</td>
                    <td class="right nowrap"><strong>{{ $category['total_nominal_formatted'] }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center muted">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Detail Absensi</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="center" style="width: 30px;">No</th>
                <th class="nowrap">Tanggal</th>
                <th class="nowrap">Datang</th>
                <th class="nowrap">Pulang</th>
                <th>Kategori</th>
                <th class="nowrap">Durasi</th>
                <th class="right nowrap">Nominal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report['details'] as $detail)
                <tr>
                    <td class="center">{{ $detail['no'] }}</td>
                    <td class="nowrap">{{ $detail['tanggal_formatted'] }}</td>
                    <td class="center nowrap">{{ $detail['jam_datang'] }}</td>
                    <td class="center nowrap">{{ $detail['jam_pulang'] }}</td>
                    <td>{{ $detail['kategori'] }}</td>
                    <td class="right nowrap">{{ $detail['durasi_teks'] }}</td>
                    <td class="right nowrap"><strong>{{ $detail['nominal_formatted'] }}</strong></td>
                    <td>{{ $detail['keterangan'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center muted">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
