@extends('layouts.app')

@section('title', 'Detail Pengajuan Transaksi')
@section('page-title', 'Detail Pengajuan')

@section('breadcrumbs')
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    @if(Auth::user()->isStaff())
        <li class="breadcrumb-item"><a href="{{ route('submissions.index') }}">Pengajuan Saya</a></li>
    @elseif(Auth::user()->isFinance())
        <li class="breadcrumb-item"><a href="{{ route('payments.index') }}">Antrian Pembayaran</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('approvals.index') }}">Antrian Approval</a></li>
    @endif
    <li class="breadcrumb-item active" aria-current="page">#{{ $submission->submission_number }}</li>
</ol>
@endsection

@section('content')
<div class="row g-4">
    <!-- Left Panel: Submission Details -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>Informasi Transaksi</h5>
                @php
                    $badgeClass = 'badge-draft';
                    if ($submission->status === 'Waiting Supervisor Approval') $badgeClass = 'badge-waiting-supervisor';
                    elseif ($submission->status === 'Waiting Manager Approval') $badgeClass = 'badge-waiting-manager';
                    elseif ($submission->status === 'Waiting Director Approval') $badgeClass = 'badge-waiting-director';
                    elseif ($submission->status === 'Waiting Finance') $badgeClass = 'badge-waiting-finance';
                    elseif ($submission->status === 'Rejected') $badgeClass = 'badge-rejected';
                    elseif ($submission->status === 'Paid') $badgeClass = 'badge-paid';
                @endphp
                <span class="badge-status {{ $badgeClass }} fs-6">{{ $submission->status }}</span>
            </div>
            
            <div class="card-body p-4 border-top">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Nomor Pengajuan</label>
                        <span class="fw-bold text-dark fs-5">{{ $submission->submission_number }}</span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Tanggal Pengajuan</label>
                        <span class="fw-medium text-dark">{{ $submission->submission_date->format('d F Y') }}</span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Pemohon</label>
                        <span class="fw-medium text-dark">{{ $submission->user->name }} ({{ $submission->user->email }})</span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Kategori Pengeluaran</label>
                        <span class="fw-medium text-dark">{{ $submission->category->name }} ({{ $submission->category->code }})</span>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Nominal Transaksi</label>
                        <span class="fw-bold text-primary fs-3">Rp {{ number_format($submission->requested_amount, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small fw-semibold text-uppercase d-block mb-2">Deskripsi Keperluan</label>
                    <div class="p-3 bg-light rounded text-dark text-break" style="white-space: pre-line; line-height: 1.5; font-size: 0.95rem;">
                        {{ $submission->description }}
                    </div>
                </div>

                <!-- Attachments Section -->
                <div>
                    <label class="text-muted small fw-semibold text-uppercase d-block mb-2">Berkas Lampiran Pendukung</label>
                    @if($submission->submissionFiles->isEmpty())
                        <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Tidak ada lampiran berkas pendukung.</p>
                    @else
                        <div class="row g-2">
                            @foreach($submission->submissionFiles as $file)
                                <div class="col-12 col-sm-6">
                                    <div class="border d-flex align-items-center justify-content-between p-3 rounded bg-light">
                                        <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                            @php
                                                $icon = 'bi-file-earmark';
                                                if (str_contains($file->file_type, 'pdf')) $icon = 'bi-file-earmark-pdf text-danger';
                                                elseif (str_contains($file->file_type, 'image')) $icon = 'bi-file-earmark-image text-success';
                                            @endphp
                                            <i class="bi {{ $icon }} fs-4 flex-shrink-0"></i>
                                            <div class="text-truncate small">
                                                <div class="fw-semibold text-dark text-truncate" title="{{ $file->file_name }}">{{ $file->file_name }}</div>
                                                <div class="text-muted">{{ number_format($file->file_size / 1024, 1) }} KB</div>
                                            </div>
                                        </div>
                                        <a href="{{ route('submissions.download', $file) }}" class="btn btn-sm btn-outline-primary-premium flex-shrink-0 px-2 py-1" title="Unduh Berkas">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center border-top">
                @if(Auth::user()->isStaff())
                    <a href="{{ route('submissions.index') }}" class="btn btn-outline-premium">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                @elseif(Auth::user()->isFinance())
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-premium">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                @else
                    <a href="{{ route('approvals.index') }}" class="btn btn-outline-premium">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                @endif

                @if($submission->status === 'Draft' && $submission->user_id === Auth::id())
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('submissions.submit', $submission) }}" class="submit-form">
                            @csrf
                            <button type="button" class="btn btn-success btn-submit-workflow">
                                <i class="bi bi-send me-1"></i>Kirim Pengajuan
                            </button>
                        </form>
                        <a href="{{ route('submissions.edit', $submission) }}" class="btn btn-warning text-white">
                            <i class="bi bi-pencil me-1"></i>Ubah
                        </a>
                        <form method="POST" action="{{ route('submissions.destroy', $submission) }}" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-delete-submission">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Receipt Block (If Paid) -->
        @if($submission->status === 'Paid' && $submission->payment)
            <div class="card border-0 shadow-sm border-start border-4 border-success" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold text-success mb-0"><i class="bi bi-receipt-cutoff me-2"></i>Bukti Pembayaran Selesai</h5>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="row g-3">
                        <div class="col-12 col-sm-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Tanggal Dibayar</span>
                            <span class="fw-medium text-dark">{{ $submission->payment->payment_date->format('d F Y') }}</span>
                        </div>
                        <div class="col-12 col-sm-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">No Referensi Bank</span>
                            <span class="fw-bold text-dark font-monospace">{{ $submission->payment->reference_number }}</span>
                        </div>
                        <div class="col-12 col-sm-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Kasir Pembayar</span>
                            <span class="fw-medium text-dark">{{ $submission->payment->paidBy->name }}</span>
                        </div>
                        @if($submission->payment->notes)
                            <div class="col-12">
                                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Catatan Pembayaran</span>
                                <span class="text-dark small">{{ $submission->payment->notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Right Panel: Approval Trail / Timeline -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shield-check text-primary me-2"></i>Jejak Approval</h5>
            </div>
            
            <div class="card-body p-4 border-top">
                <div class="position-relative">
                    <!-- Vertical line -->
                    <div class="position-absolute start-0 h-100 border-start border-2 border-secondary-subtle" style="left: 15px !important; z-index: 1;"></div>
                    
                    @forelse($submission->approvals as $approval)
                        @php
                            $timelineIcon = 'bi-circle';
                            $timelineBg = 'bg-secondary';
                            if ($approval->action === 'Submit') {
                                $timelineIcon = 'bi-send-fill';
                                $timelineBg = 'bg-primary text-white';
                            } elseif ($approval->action === 'Approve') {
                                $timelineIcon = 'bi-check-lg';
                                $timelineBg = 'bg-success text-white';
                            } elseif ($approval->action === 'Reject') {
                                $timelineIcon = 'bi-x-lg';
                                $timelineBg = 'bg-danger text-white';
                            }
                        @endphp
                        <div class="d-flex mb-4 position-relative" style="z-index: 2;">
                            <!-- Circle indicator -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.85rem; {{ $timelineBg === 'bg-success text-white' ? 'background-color: #10b981;' : ($timelineBg === 'bg-danger text-white' ? 'background-color: #ef4444;' : ($timelineBg === 'bg-primary text-white' ? 'background-color: #3b82f6;' : 'background-color: #94a3b8;')) }} color: white;">
                                <i class="bi {{ $timelineIcon }}"></i>
                            </div>
                            <!-- Content -->
                            <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold text-dark mb-0 small">{{ $approval->user->name }}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $approval->created_at->format('d-m-Y H:i') }}</small>
                                </div>
                                <div class="small mb-1">
                                    <span class="badge bg-secondary-subtle text-secondary py-0.5 px-1 me-1" style="font-size: 0.6rem; text-transform: uppercase;">{{ $approval->role }}</span>
                                    <span class="fw-semibold {{ $approval->action === 'Approve' ? 'text-success' : ($approval->action === 'Reject' ? 'text-danger' : 'text-primary') }}" style="font-size: 0.8rem;">
                                        {{ $approval->action === 'Submit' ? 'Mengajukan' : ($approval->action === 'Approve' ? 'Menyetujui' : 'Menolak') }}
                                    </span>
                                </div>
                                @if($approval->notes)
                                    <div class="p-2 bg-light rounded text-muted text-break" style="font-size: 0.78rem; font-style: italic;">
                                        "{{ $approval->notes }}"
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-hourglass-split fs-2"></i>
                            <p class="mt-2 mb-0 small">Belum ada jejak persetujuan workflow.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Confirmation for delete
        const deleteButton = document.querySelector('.btn-delete-submission');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
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
        }

        // Confirmation for submit workflow
        const submitButton = document.querySelector('.btn-submit-workflow');
        if (submitButton) {
            submitButton.addEventListener('click', function(e) {
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
        }
    });
</script>
@endsection
