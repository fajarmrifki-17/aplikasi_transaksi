@extends('layouts.app')

@section('title', 'Proses Pembayaran Transaksi')
@section('page-title', 'Proses Pembayaran')

@section('breadcrumbs')
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payments.index') }}">Antrian Pembayaran</a></li>
    <li class="breadcrumb-item active" aria-current="page">#{{ $submission->submission_number }}</li>
</ol>
@endsection

@section('content')
<div class="row g-4">
    <!-- Left Column: Details & Form -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>Detail Transaksi</h5>
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
                        <span class="fw-medium text-dark">{{ $submission->category->name }}</span>
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

                <!-- Attachments -->
                <div class="mb-2">
                    <label class="text-muted small fw-semibold text-uppercase d-block mb-2">Berkas Lampiran Pendukung</label>
                    @if($submission->submissionFiles->isEmpty())
                        <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Tidak ada lampiran berkas pendukung.</p>
                    @else
                        <div class="row g-2">
                            @foreach($submission->submissionFiles as $file)
                                <div class="col-12 col-sm-6">
                                    <div class="border d-flex align-items-center justify-content-between p-3 rounded bg-light">
                                        <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                            <i class="bi bi-file-earmark-check text-success fs-4 flex-shrink-0"></i>
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
        </div>

        <!-- Budget Validation Panel & Form -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-wallet2 text-success me-2"></i>Validasi Anggaran & Pembayaran</h5>
            </div>
            
            <div class="card-body p-4 border-top">
                <!-- Budget Status Alert Box -->
                @if($hasBudget)
                    <div class="alert alert-success border-0 d-flex align-items-start gap-3 mb-4" role="alert" style="border-radius: 10px;">
                        <i class="bi bi-check-circle-fill fs-4 mt-0.5"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-1">Anggaran Tersedia (Sufficient Budget)</h6>
                            <p class="mb-0 small text-success-emphasis">
                                Saldo anggaran kategori <strong>{{ $submission->category->name }}</strong> untuk tahun fiskal {{ date('Y', strtotime($submission->submission_date)) }} masih mencukupi untuk memproses pengeluaran ini.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="alert alert-danger border-0 d-flex align-items-start gap-3 mb-4" role="alert" style="border-radius: 10px;">
                        <i class="bi bi-exclamation-triangle-fill fs-4 mt-0.5"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-1">Anggaran Tidak Mencukupi (Insufficient Budget)</h6>
                            <p class="mb-0 small text-danger-emphasis">
                                <strong>Peringatan!</strong> Sisa saldo anggaran kategori <strong>{{ $submission->category->name }}</strong> tahun ini tidak mencukupi. Menekan tombol "Proses Pembayaran" akan otomatis <strong>MENOLAK (REJECT)</strong> pengajuan ini sesuai dengan Business Rule sistem.
                            </p>
                        </div>
                    </div>
                @endif

                <form id="paymentForm" method="POST" action="{{ route('payments.pay', $submission) }}">
                    @csrf

                    <div class="row g-3 mb-3">
                        <!-- Payment Date -->
                        <div class="col-12 col-sm-6">
                            <label for="payment_date" class="form-label fw-semibold text-muted small">Tanggal Pembayaran <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control bg-light @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', now()->toDateString()) }}" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bank Reference Number -->
                        <div class="col-12 col-sm-6">
                            <label for="reference_number" class="form-label fw-semibold text-muted small">No. Referensi Bank / Transfer <span class="text-danger">*</span></label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control bg-light @error('reference_number') is-invalid @enderror" placeholder="Contoh: TRX-98124018" value="{{ old('reference_number') }}" required>
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold text-muted small">Catatan Pembayaran <span class="text-muted">(Opsional)</span></label>
                        <textarea name="notes" id="notes" class="form-control bg-light @error('notes') is-invalid @enderror" rows="3" placeholder="Tuliskan catatan transaksi bank, kode transfer, dll...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-premium">
                            Batal
                        </a>
                        @if($hasBudget)
                            <button type="submit" class="btn btn-success" id="btnDisburse">
                                <i class="bi bi-cash-coin me-1"></i>Proses Pembayaran (Paid)
                            </button>
                        @else
                            <button type="submit" class="btn btn-danger" id="btnRejectDisburse">
                                <i class="bi bi-x-circle me-1"></i>Tolak Pengajuan (Over-budget)
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Timeline -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Timeline Persetujuan</h5>
            </div>
            
            <div class="card-body p-4 border-top">
                <div class="position-relative">
                    <div class="position-absolute start-0 h-100 border-start border-2 border-secondary-subtle" style="left: 15px !important; z-index: 1;"></div>
                    
                    @foreach($submission->approvals as $approval)
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
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.85rem; {{ $timelineBg === 'bg-success text-white' ? 'background-color: #10b981;' : ($timelineBg === 'bg-danger text-white' ? 'background-color: #ef4444;' : ($timelineBg === 'bg-primary text-white' ? 'background-color: #3b82f6;' : 'background-color: #94a3b8;')) }} color: white;">
                                <i class="bi {{ $timelineIcon }}"></i>
                            </div>
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
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('paymentForm');
        const hasBudget = {{ $hasBudget ? 'true' : 'false' }};

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const title = hasBudget ? 'Bayar Pengajuan?' : 'Tolak & Batalkan Pengajuan?';
            const text = hasBudget 
                ? 'Dana alokasi anggaran akan dikurangi secara otomatis senilai nominal pengajuan.' 
                : 'Peringatan! Anggaran kategori tidak mencukupi. Sistem akan membatalkan pembayaran dan menolak pengajuan ini.';
            const confirmBtnText = hasBudget ? 'Ya, Bayar!' : 'Ya, Tolak!';
            const icon = hasBudget ? 'success' : 'error';
            const btnColor = hasBudget ? '#10b981' : '#ef4444';

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: btnColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmBtnText,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
