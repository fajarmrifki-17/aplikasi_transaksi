<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengajuan Transaksi Pengeluaran</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #fff;
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .report-header {
            border-bottom: 3px double #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .table-print th {
            background-color: #f2f2f2 !important;
            color: #000 !important;
            border-color: #333 !important;
            font-weight: bold;
            text-align: center;
        }
        .table-print td {
            border-color: #333 !important;
        }
        .signatures {
            margin-top: 50px;
        }
        .signature-box {
            text-align: center;
            height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 10px;
            }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="container-fluid py-4">
        <!-- Floating Close button for web preview -->
        <div class="text-end mb-3 no-print">
            <button onclick="window.close();" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg"></i> Tutup Halaman</button>
            <button onclick="window.print();" class="btn btn-primary btn-sm">Cetak Ulang</button>
        </div>

        <!-- Corporate Header -->
        <div class="report-header text-center">
            <h3 class="mb-0 fw-bold">PT. ENTERPRISE MAKMUR SENTOSA</h3>
            <p class="mb-1 text-muted">Jl. Jenderal Sudirman No. 45, Jakarta Selatan | Telp: (021) 555-0199</p>
            <hr class="my-2" style="border-top: 2px solid #000;">
            <div class="report-title mt-2">Laporan Pengajuan Transaksi Pengeluaran</div>
            <div class="text-muted small mt-1">Dicetak pada: {{ date('d/m/Y H:i:s') }}</div>
        </div>

        <!-- Filters summary -->
        <div class="mb-4">
            <div class="row">
                <div class="col-6">
                    <strong>Parameter Laporan:</strong>
                    <table class="table table-sm table-borderless mb-0 small">
                        <tr>
                            <td width="30%">Mulai Tanggal</td>
                            <td>: {{ request('start_date') ? date('d/m/Y', strtotime(request('start_date'))) : 'Semua Tanggal' }}</td>
                        </tr>
                        <tr>
                            <td>Sampai Tanggal</td>
                            <td>: {{ request('end_date') ? date('d/m/Y', strtotime(request('end_date'))) : 'Semua Tanggal' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-6 text-end">
                    <table class="table table-sm table-borderless mb-0 small float-end" style="width: auto;">
                        <tr>
                            <td class="text-start" width="120px">Departemen</td>
                            <td class="text-start">: {{ request('department_id') ? \App\Models\Department::find(request('department_id'))->name : 'Semua Departemen' }}</td>
                        </tr>
                        <tr>
                            <td class="text-start">Status Pengajuan</td>
                            <td class="text-start">: {{ request('status') ?: 'Semua Status' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Main Items Table -->
        <table class="table table-bordered table-print align-middle">
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="15%">No. Pengajuan</th>
                    <th width="10%">Tanggal</th>
                    <th width="15%">Pengaju</th>
                    <th width="10%">Dept</th>
                    <th width="12%">Kategori</th>
                    <th>Vendor</th>
                    <th width="12%">Nominal</th>
                    <th width="10%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenseRequests as $index => $request)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $request->request_number }}</td>
                        <td class="text-center">{{ $request->request_date->format('d/m/Y') }}</td>
                        <td>{{ $request->user->name }}</td>
                        <td class="text-center">{{ $request->department->code }}</td>
                        <td>{{ $request->category->name }}</td>
                        <td>{{ $request->vendor }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($request->amount, 0, ',', '.') }}</td>
                        <td class="text-center text-uppercase fw-bold" style="font-size: 9px;">{{ $request->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">Tidak ada data transaksi yang ditemukan.</td>
                    </tr>
                @endforelse
                <tr class="fw-bold" style="background-color: #f9f9f9;">
                    <td colspan="7" class="text-end">TOTAL KESELURUHAN :</td>
                    <td class="text-end text-primary">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures block -->
        <div class="row signatures text-center">
            <div class="col-4">
                <div class="signature-box">
                    <span>Dibuat Oleh,</span>
                    <div>
                        <hr class="my-1 mx-5" style="border-top: 1px solid #000;">
                        <span class="small text-muted">Finance & Accounting</span>
                    </div>
                </div>
            </div>
            
            <div class="col-4">
                <div class="signature-box">
                    <span>Diperiksa Oleh,</span>
                    <div>
                        <hr class="my-1 mx-5" style="border-top: 1px solid #000;">
                        <span class="small text-muted">Manager Keuangan</span>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="signature-box">
                    <span>Disetujui Oleh,</span>
                    <div>
                        <hr class="my-1 mx-5" style="border-top: 1px solid #000;">
                        <span class="small text-muted">Direktur Utama</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
