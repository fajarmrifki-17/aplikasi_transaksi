@extends('layouts.app')

@section('title', 'Dashboard Supervisor')
@section('page-title', 'Dashboard Supervisor')

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
        <div class="col-md-6 col-xl-4">
            <div class="card card-stat bg-white h-100">
                <div class="card-body">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Menunggu Approval</h6>
                        <h3 class="mb-0 fw-bold text-primary">{{ $stats['waiting_approval'] }}</h3>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card card-stat bg-white h-100">
                <div class="card-body">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Disetujui Hari Ini</h6>
                        <h3 class="mb-0 fw-bold text-success">{{ $stats['approved_today'] }}</h3>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="card custom-table-card">
        <div class="card-header">
            <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-list-task me-2 text-primary"></i>Menunggu Persetujuan Anda</h5>
            <a href="{{ route('approvals.index') }}" class="btn btn-premium btn-outline-premium btn-sm">
                Lihat Semua
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
                                <a href="{{ route('approvals.show', $request->id) }}" class="btn btn-sm btn-premium btn-primary-premium">
                                    <i class="bi bi-check2-square"></i> Proses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada pengajuan yang membutuhkan approval saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
