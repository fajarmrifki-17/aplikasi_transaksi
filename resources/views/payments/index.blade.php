@extends('layouts.app')

@section('title', 'Validasi & Pembayaran Transaksi')
@section('page-title', 'Antrian Validasi & Pembayaran')

@section('breadcrumbs')
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Antrian Pembayaran</li>
</ol>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body p-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('payments.index') }}" class="row g-3">
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
                    <!-- Default value represents 'Waiting Finance' -->
                    <option value="Waiting Finance" {{ request('status', 'Waiting Finance') === 'Waiting Finance' ? 'selected' : '' }}>Waiting Finance</option>
                    <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid (Selesai Dibayar)</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
                    <option value="" {{ request('status') === '' ? 'selected' : '' }}>Semua Transaksi</option>
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
                <a href="{{ route('payments.index') }}" class="btn btn-outline-premium w-100 py-2 d-flex align-items-center justify-content-center" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Payments Table Card -->
<div class="card custom-table-card border-0">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h5 class="fw-bold text-dark mb-0">Antrian Pengeluaran Dana</h5>
    </div>
    
    <div class="table-responsive">
        <table class="table custom-table align-middle">
            <thead>
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Pemohon</th>
                    <th>Kategori</th>
                    <th>Nominal</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th class="text-end">Tindakan</th>
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
                        <td>{{ $submission->submission_date->format('d-m-Y') }}</td>
                        <td>
                            @php
                                $badgeClass = 'badge-waiting-finance';
                                if ($submission->status === 'Paid') $badgeClass = 'badge-paid';
                                elseif ($submission->status === 'Rejected') $badgeClass = 'badge-rejected';
                            @endphp
                            <span class="badge-status {{ $badgeClass }}">{{ $submission->status }}</span>
                        </td>
                        <td class="text-end">
                            @if($submission->status === 'Waiting Finance')
                                <a href="{{ route('payments.show', $submission) }}" class="btn btn-sm btn-success py-1 px-3 border-0" style="font-weight: 500;">
                                    <i class="bi bi-cash-stack"></i> Proses Bayar
                                </a>
                            @else
                                <a href="{{ route('submissions.show', $submission) }}" class="btn btn-sm btn-outline-premium py-1 px-3">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-patch-check fs-2 text-success"></i>
                            <p class="mt-2 mb-0 fw-semibold text-dark">Antrian Pembayaran Kosong</p>
                            <p class="text-muted small">Tidak ada transaksi yang memerlukan tindakan pembayaran saat ini.</p>
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
