@extends('layouts.app')

@section('title', 'Daftar Pengajuan Transaksi')
@section('page-title', 'Pengajuan Transaksi Saya')

@section('breadcrumbs')
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pengajuan Saya</li>
</ol>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body p-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('submissions.index') }}" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-muted">Cari</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Nomor / Deskripsi..." value="{{ request('search') }}">
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
                    <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Submitted" {{ request('status') === 'Submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="Waiting Supervisor Approval" {{ request('status') === 'Waiting Supervisor Approval' ? 'selected' : '' }}>Waiting Supervisor</option>
                    <option value="Waiting Manager Approval" {{ request('status') === 'Waiting Manager Approval' ? 'selected' : '' }}>Waiting Manager</option>
                    <option value="Waiting Director Approval" {{ request('status') === 'Waiting Director Approval' ? 'selected' : '' }}>Waiting Director</option>
                    <option value="Waiting Finance" {{ request('status') === 'Waiting Finance' ? 'selected' : '' }}>Waiting Finance</option>
                    <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
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
                <a href="{{ route('submissions.index') }}" class="btn btn-outline-premium w-100 py-2 d-flex align-items-center justify-content-center" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Submissions Table Card -->
<div class="card custom-table-card border-0">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">Daftar Pengajuan</h5>
        <a href="{{ route('submissions.create') }}" class="btn btn-primary-premium">
            <i class="bi bi-plus-lg"></i> Buat Pengajuan
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="table custom-table align-middle">
            <thead>
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Kategori</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                    <tr>
                        <td class="fw-semibold text-dark">{{ $submission->submission_number }}</td>
                        <td>{{ $submission->submission_date->format('d F Y') }}</td>
                        <td>
                            <span class="fw-medium">{{ $submission->category->name }}</span>
                            <br><small class="text-muted text-uppercase" style="font-size: 0.65rem;">{{ $submission->category->code }}</small>
                        </td>
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
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('submissions.show', $submission) }}" class="btn btn-sm btn-outline-premium px-2 py-1" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>

                                @if($submission->status === 'Draft')
                                    <!-- Submit to Workflow button -->
                                    <form method="POST" action="{{ route('submissions.submit', $submission) }}" class="submit-form">
                                        @csrf
                                        <button type="button" class="btn btn-sm btn-success px-2 py-1 btn-submit-workflow" title="Kirim ke Workflow">
                                            <i class="bi bi-send"></i>
                                        </button>
                                    </form>

                                    <!-- Edit button -->
                                    <a href="{{ route('submissions.edit', $submission) }}" class="btn btn-sm btn-warning text-white px-2 py-1" title="Ubah Pengajuan">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <!-- Delete button -->
                                    <form method="POST" action="{{ route('submissions.destroy', $submission) }}" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger px-2 py-1 btn-delete-submission" title="Hapus Pengajuan">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-2"></i>
                            <p class="mt-2 mb-0">Tidak ada pengajuan transaksi yang ditemukan.</p>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Confirmation for delete
        const deleteButtons = document.querySelectorAll('.btn-delete-submission');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.delete-form');
                Swal.fire({
                    title: 'Hapus Pengajuan?',
                    text: 'Pengajuan draft ini akan dihapus secara permanen beserta berkas pendukungnya.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Confirmation for submit workflow
        const submitButtons = document.querySelectorAll('.btn-submit-workflow');
        submitButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.submit-form');
                Swal.fire({
                    title: 'Kirim Pengajuan?',
                    text: 'Pengajuan akan dikirim ke workflow persetujuan dan tidak dapat diubah lagi.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Kirim!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
