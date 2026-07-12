<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengajuan Transaksi Pengeluaran</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 10px;
        }
        .logo-text {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .subtitle {
            font-size: 12px;
            color: #0d9488;
            font-weight: 600;
            margin: 2px 0 0 0;
        }
        .meta-info {
            margin-top: 5px;
            font-size: 9px;
            color: #64748b;
        }
        .filter-summary {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .filter-summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .filter-summary td {
            font-size: 9px;
            color: #475569;
            padding: 2px 5px;
            border: none;
        }
        .filter-label {
            font-weight: bold;
            width: 80px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .report-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #1e293b;
            text-align: left;
        }
        .report-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .report-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .report-table .text-right {
            text-align: right;
        }
        .report-table .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .text-dark {
            color: #0f172a;
        }
        .badge-status {
            padding: 3px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 20px;
            text-transform: uppercase;
            display: inline-block;
        }
        .badge-draft { background-color: #e2e8f0; color: #475569; }
        .badge-waiting { background-color: #dbeafe; color: #1d4ed8; }
        .badge-waiting-finance { background-color: #d1fae5; color: #047857; }
        .badge-rejected { background-color: #fee2e2; color: #b91c1c; }
        .badge-approved { background-color: #d1fae5; color: #065f46; }
        .badge-paid { background-color: #cffafe; color: #0891b2; }

        .footer-summary {
            font-size: 12px;
            background-color: #f1f5f9;
        }
        .footer-stamp {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border-collapse: collapse; border: none;">
            <tr>
                <td>
                    <h1 class="logo-text">SISTEM PENGAJUAN TRANSAKSI PENGELUARAN</h1>
                    <div class="subtitle">Laporan Rekapitulasi Pengeluaran Dana (Landscape)</div>
                </td>
                <td style="text-align: right; vertical-align: bottom;">
                    <div class="meta-info">Tanggal Cetak: {{ date('d-m-Y H:i:s') }}</div>
                    <div class="meta-info">Dicetak Oleh: {{ auth()->user()->name }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Filter Summary -->
    <div class="filter-summary">
        <table>
            <tr>
                <td class="filter-label">Kata Kunci:</td>
                <td>{{ $filters['search'] ?? 'Semua Kata Kunci' }}</td>
                <td class="filter-label">Periode Mulai:</td>
                <td>{{ !empty($filters['start_date']) ? date('d-m-Y', strtotime($filters['start_date'])) : 'Awal Sistem' }}</td>
            </tr>
            <tr>
                <td class="filter-label">Status:</td>
                <td>{{ $filters['status'] ?? 'Semua Status' }}</td>
                <td class="filter-label">Periode Selesai:</td>
                <td>{{ !empty($filters['end_date']) ? date('d-m-Y', strtotime($filters['end_date'])) : 'Hari Ini' }}</td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">No</th>
                <th style="width: 15%;">No. Pengajuan</th>
                <th style="width: 15%;">Pemohon</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 10%;" class="text-center">Status</th>
                <th style="width: 12%;" class="text-center">Tanggal Bayar</th>
                <th style="width: 13%;">No. Ref Bank</th>
                <th style="width: 13%;" class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($submissions as $sub)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="fw-bold text-dark">{{ $sub->submission_number }}</td>
                    <td>{{ $sub->user->name }}</td>
                    <td>{{ $sub->category->name }}</td>
                    <td>{{ $sub->submission_date->format('d-m-Y') }}</td>
                    <td class="text-center">
                        @php
                            $statusBadge = 'badge-draft';
                            if (str_contains($sub->status, 'Waiting') && !str_contains($sub->status, 'Finance')) $statusBadge = 'badge-waiting';
                            elseif ($sub->status === 'Waiting Finance') $statusBadge = 'badge-waiting-finance';
                            elseif ($sub->status === 'Rejected') $statusBadge = 'badge-rejected';
                            elseif ($sub->status === 'Paid') $statusBadge = 'badge-paid';
                            elseif ($sub->status === 'Approved') $statusBadge = 'badge-approved';
                        @endphp
                        <span class="badge-status {{ $statusBadge }}">{{ $sub->status }}</span>
                    </td>
                    <td class="text-center">{{ $sub->payment && $sub->payment->payment_date ? $sub->payment->payment_date->format('d-m-Y') : '-' }}</td>
                    <td class="font-monospace">{{ $sub->payment ? $sub->payment->reference_number : '-' }}</td>
                    <td class="text-right fw-bold text-dark">Rp {{ number_format($sub->requested_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 30px; color: #64748b;">Tidak ada pengajuan transaksi yang cocok dengan kriteria filter.</td>
                </tr>
            @endforelse
            
            <!-- Summary Row -->
            @if($submissions->isNotEmpty())
                <tr class="footer-summary">
                    <td colspan="8" class="text-right fw-bold text-dark" style="padding: 10px;">TOTAL TRANSAKSI :</td>
                    <td class="text-right fw-bold text-dark" style="padding: 10px; font-size: 11px;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer-stamp">
        <p>Sistem Pengajuan Transaksi Pengeluaran &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
