@extends('layouts.app')

@section('title', 'Daftar Pengajuan Transaksi')
@section('page-title', 'Daftar Pengajuan Transaksi')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb custom-breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengajuan Saya</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row g-4 mb-4">
        <!-- Search and Filter Form -->
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4">
                <form method="GET" action="{{ route('expense-requests.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="form-label fw-semibold text-muted small">Cari No. Pengajuan / Vendor / Deskripsi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-0" id="search" name="search" value="{{ request('search') }}" placeholder="Masukkan kata kunci...">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="status" class="form-label fw-semibold text-muted small">Filter Status</label>
                        <select class="form-select bg-light border-0" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Waiting Supervisor" {{ request('status') === 'Waiting Supervisor' ? 'selected' : '' }}>Waiting Supervisor</option>
                            <option value="Waiting Manager" {{ request('status') === 'Waiting Manager' ? 'selected' : '' }}>Waiting Manager</option>
                            <option value="Waiting Director" {{ request('status') === 'Waiting Director' ? 'selected' : '' }}>Waiting Director</option>
                            <option value="Waiting Finance" {{ request('status') === 'Waiting Finance' ? 'selected' : '' }}>Waiting Finance</option>
                            <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved (Ready to Pay)</option>
                            <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-premium btn-primary-premium flex-grow-1">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('expense-requests.index') }}" class="btn btn-light" title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Expense Requests Table -->
        <div class="col-12">
            <div class="card custom-table-card">
                <div class="card-header">
                    <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-wallet2 text-primary me-2"></i>Riwayat Pengajuan</h5>
                    <a href="{{ route('expense-requests.create') }}" class="btn btn-premium btn-primary-premium">
                        <i class="bi bi-plus-lg"></i> Tambah Pengajuan
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>No. Pengajuan</th>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Vendor</th>
                                <th>Nominal</th>
                                <th>Urgensi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($expenseRequests as $request)
                                <tr>
                                    <td class="fw-semibold">{{ $request->request_number }}</td>
                                    <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                    <td>{{ $request->category->name }}</td>
                                    <td>{{ $request->vendor }}</td>
                                    <td class="fw-semibold text-primary">Rp {{ number_format($request->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $urgencyBadge = 'bg-secondary';
                                            if($request->urgency === 'High') $urgencyBadge = 'bg-warning text-dark';
                                            if($request->urgency === 'Urgent') $urgencyBadge = 'bg-danger';
                                            if($request->urgency === 'Medium') $urgencyBadge = 'bg-info text-white';
                                        @endphp
                                        <span class="badge {{ $urgencyBadge }}">{{ $request->urgency }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = 'badge-draft';
                                            switch($request->status) {
                                                case 'Waiting Supervisor': $badgeClass = 'badge-waiting-supervisor'; break;
                                                case 'Waiting Manager': $badgeClass = 'badge-waiting-manager'; break;
                                                case 'Waiting Director': $badgeClass = 'badge-waiting-director'; break;
                                                case 'Waiting Finance': $badgeClass = 'badge-waiting-finance'; break;
                                                case 'Approved': $badgeClass = 'badge-approved'; break;
                                                case 'Rejected': $badgeClass = 'badge-rejected'; break;
                                                case 'Paid': $badgeClass = 'badge-paid'; break;
                                            }
                                        @endphp
                                        <span class="badge-status {{ $badgeClass }}">{{ $request->status }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Aksi
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('expense-requests.show', $request->id) }}">
                                                        <i class="bi bi-eye text-muted"></i> Detail
                                                    </a>
                                                </li>
                                                @if($request->status === 'Draft')
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('expense-requests.edit', $request->id) }}">
                                                            <i class="bi bi-pencil text-muted"></i> Edit Draft
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('expense-requests.submit', $request->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-success">
                                                                <i class="bi bi-send-fill text-success"></i> Submit Pengajuan
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center gap-2 text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $request->id }}">
                                                            <i class="bi bi-trash text-danger"></i> Hapus Draft
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>

                                        <!-- Delete Modal -->
                                        @if($request->status === 'Draft')
                                            <div class="modal fade" id="deleteModal{{ $request->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $request->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0">
                                                        <div class="modal-header border-0 pb-0">
                                                            <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel{{ $request->id }}"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body py-3">
                                                            Apakah Anda yakin ingin menghapus pengajuan draft <strong>{{ $request->request_number }}</strong>? Tindakan ini tidak dapat dibatalkan.
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('expense-requests.destroy', $request->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 mb-2 d-block"></i>
                                        Belum ada pengajuan transaksi pengeluaran.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($expenseRequests->hasPages())
                    <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                        {{ $expenseRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
