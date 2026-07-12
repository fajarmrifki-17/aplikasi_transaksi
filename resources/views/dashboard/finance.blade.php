@extends('layouts.app')

@section('title', 'Dashboard Finance')
@section('page-title', 'Dashboard Finance')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb custom-breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-xl-3">
            <div class="card card-stat bg-white h-100">
                <div class="card-body">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Menunggu Validasi</h6>
                        <h3 class="mb-0 fw-bold text-warning">{{ $stats['waiting_validation'] }}</h3>
                    </div>
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card card-stat bg-white h-100">
                <div class="card-body">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Siap Dibayar</h6>
                        <h3 class="mb-0 fw-bold text-primary">{{ $stats['waiting_payment'] }}</h3>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card card-stat bg-white h-100">
                <div class="card-body">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Transaksi Paid</h6>
                        <h3 class="mb-0 fw-bold text-success">{{ $stats['total_paid'] }}</h3>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card card-stat bg-white h-100">
                <div class="card-body">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Total Pembayaran</h6>
                        <h5 class="mb-0 fw-bold text-info mt-1">Rp {{ number_format($stats['total_amount_paid'], 0, ',', '.') }}</h5>
                    </div>
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Payments & Validations -->
    <div class="card custom-table-card">
        <div class="card-header">
            <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-cash me-2 text-primary"></i>Antrean Validasi & Pembayaran</h5>
            <a href="{{ route('payments.index') }}" class="btn btn-premium btn-primary-premium btn-sm">
                Kelola Pembayaran
            </a>
        </div>
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Tanggal</th>
                        <th>Pengaju</th>
                        <th>Departemen</th>
                        <th>Kategori</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRequests as $request)
                        <tr>
                            <td class="fw-semibold">{{ $request->request_number }}</td>
                            <td>{{ $request->request_date->format('d/m/Y') }}</td>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->user->department->code ?? '-' }}</td>
                            <td>{{ $request->category->name }}</td>
                            <td class="fw-semibold text-primary">Rp {{ number_format($request->amount, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $badgeClass = 'badge-draft';
                                    switch($request->status) {
                                        case 'Waiting Finance': $badgeClass = 'badge-waiting-finance'; break;
                                        case 'Approved': $badgeClass = 'badge-approved'; break;
                                    }
                                @endphp
                                <span class="badge-status {{ $badgeClass }}">{{ $request->status === 'Approved' ? 'Ready to Pay' : 'Waiting Finance' }}</span>
                            </td>
                            <td>
                                <a href="{{ route('payments.show', $request->id) }}" class="btn btn-sm btn-premium btn-primary-premium">
                                    <i class="bi bi-arrow-right-short"></i> Proses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada antrean validasi atau pembayaran saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
