@extends('layouts.app')

@section('title', 'Laporan Pengeluaran Dana')
@section('page-title', 'Laporan Pengeluaran')

@section('breadcrumbs')
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Laporan Pengeluaran</li>
</ol>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body p-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('reports.index') }}" id="reportFilterForm" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-muted">Cari</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Nomor / Pemohon..." value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-semibold text-muted">Kategori</label>
                <select name="category_id" class="form-select bg-light">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-semibold text-muted">Status</label>
                <select name="status" class="form-select bg-light">
                    <option value="">Semua Status</option>
                    <option value="Waiting Supervisor Approval" {{ request('status') === 'Waiting Supervisor Approval' ? 'selected' : '' }}>Waiting Supervisor</option>
                    <option value="Waiting Manager Approval" {{ request('status') === 'Waiting Manager Approval' ? 'selected' : '' }}>Waiting Manager</option>
                    <option value="Waiting Director Approval" {{ request('status') === 'Waiting Director Approval' ? 'selected' : '' }}>Waiting Director</option>
                    <option value="Waiting Finance" {{ request('status') === 'Waiting Finance' ? 'selected' : '' }}>Waiting Finance</option>
                    <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid (Selesai Dibayar)</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
                </select>
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-semibold text-muted">Mulai Tanggal</label>
                <input type="date" name="start_date" class="form-control bg-light" value="{{ request('start_date') }}">
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control bg-light" value="{{ request('end_date') }}">
            </div>

            <div class="col-12 col-md-1 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary-premium w-100 py-2 d-flex align-items-center justify-content-center" title="Terapkan Filter">
                    <i class="bi bi-funnel"></i>
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-premium w-100 py-2 d-flex align-items-center justify-content-center" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Reports Table Card -->
<div class="card custom-table-card border-0 mb-4">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <h5 class="fw-bold text-dark mb-0">Hasil Analisa Laporan</h5>
        
        <div class="d-inline-flex gap-2">
            <!-- Export to PDF with current filter queries -->
            <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-danger py-2 px-3 border-0 d-flex align-items-center gap-2 btn-export" style="background-color: #ef4444;" title="Ekspor ke PDF">
                <i class="bi bi-file-earmark-pdf-fill"></i> Ekspor PDF
            </a>
            
            <!-- Export to CSV/Excel with current filter queries -->
            <a href="{{ route('reports.export.csv', request()->query()) }}" class="btn btn-success py-2 px-3 border-0 d-flex align-items-center gap-2 btn-export" style="background-color: #10b981;" title="Ekspor ke Excel/CSV">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Ekspor Excel
            </a>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table custom-table align-middle">
            <thead>
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Pemohon</th>
                    <th>Kategori</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Tgl Pembayaran</th>
                    <th>No Ref Bank</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                    <tr>
                        <td class="fw-semibold text-dark">{{ $submission->submission_number }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $submission->user->name }}</div>
                            <small class="text-muted" style="font-size: 0.75rem;">{{ $submission->user->email }}</small>
                        </td>
                        <td>{{ $submission->category->name }}</td>
                        <td class="fw-bold text-dark">Rp {{ number_format($submission->requested_amount, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $badgeClass = 'badge-draft';
                                if ($submission->status === 'Waiting Supervisor Approval') $badgeClass = 'badge-waiting-supervisor';
                                elseif ($submission->status === 'Waiting Manager Approval') $badgeClass = 'badge-waiting-manager';
                                elseif ($submission->status === 'Waiting Director Approval') $badgeClass = 'badge-waiting-director';
                                elseif ($submission->status === 'Waiting Finance') $badgeClass = 'badge-waiting-finance';
                                elseif ($submission->status === 'Rejected') $badgeClass = 'badge-rejected';
                                elseif ($submission->status === 'Paid') $badgeClass = 'badge-paid';
                            @endphp
                            <span class="badge-status {{ $badgeClass }}">{{ $submission->status }}</span>
                        </td>
                        <td>
                            @if($submission->payment)
                                {{ $submission->payment->payment_date->format('d-m-Y') }}
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="font-monospace text-dark small">
                            @if($submission->payment)
                                {{ $submission->payment->reference_number }}
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-break fs-2"></i>
                            <p class="mt-2 mb-0">Tidak ada pengajuan transaksi yang cocok dengan kriteria filter.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($submissions->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-4">
            {{ $submissions->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
