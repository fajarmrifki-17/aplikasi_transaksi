@extends('layouts.app')

@section('title', 'Detail Pengajuan')
@section('page-title', 'Detail Pengajuan Transaksi')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb custom-breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('expense-requests.index') }}">Pengajuan Saya</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $expenseRequest->request_number }}</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row g-4">
        <!-- Main Detail Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Pengajuan</h5>
                        
                        @php
                            $badgeClass = 'badge-draft';
                            switch($expenseRequest->status) {
                                case 'Waiting Supervisor': $badgeClass = 'badge-waiting-supervisor'; break;
                                case 'Waiting Manager': $badgeClass = 'badge-waiting-manager'; break;
                                case 'Waiting Director': $badgeClass = 'badge-waiting-director'; break;
                                case 'Waiting Finance': $badgeClass = 'badge-waiting-finance'; break;
                                case 'Approved': $badgeClass = 'badge-approved'; break;
                                case 'Rejected': $badgeClass = 'badge-rejected'; break;
                                case 'Paid': $badgeClass = 'badge-paid'; break;
                            }
                        @endphp
                        <span class="badge-status {{ $badgeClass }} fs-6">{{ $expenseRequest->status }}</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Nomor Pengajuan</label>
                            <div class="fw-semibold text-dark fs-5">{{ $expenseRequest->request_number }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Tanggal Pengajuan</label>
                            <div class="fw-semibold text-dark fs-5">{{ $expenseRequest->request_date->format('d/m/Y') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Staff Pengaju</label>
                            <div class="fw-semibold text-dark">{{ $expenseRequest->user->name }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Departemen</label>
                            <div class="fw-semibold text-dark">{{ $expenseRequest->department->name }} ({{ $expenseRequest->department->code }})</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Kategori Pengeluaran</label>
                            <div class="fw-semibold text-dark">{{ $expenseRequest->category->name }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Vendor / Penerima</label>
                            <div class="fw-semibold text-dark">{{ $expenseRequest->vendor }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Nominal Transaksi</label>
                            <div class="fw-bold text-primary fs-4">Rp {{ number_format($expenseRequest->amount, 0, ',', '.') }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Urgensi</label>
                            @php
                                $urgencyBadge = 'bg-secondary';
                                if($expenseRequest->urgency === 'High') $urgencyBadge = 'bg-warning text-dark';
                                if($expenseRequest->urgency === 'Urgent') $urgencyBadge = 'bg-danger';
                                if($expenseRequest->urgency === 'Medium') $urgencyBadge = 'bg-info text-white';
                            @endphp
                            <span class="badge {{ $urgencyBadge }} fs-6">{{ $expenseRequest->urgency }}</span>
                        </div>

                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Tanggal Dibutuhkan</label>
                            <div class="fw-semibold text-dark">{{ $expenseRequest->needed_date->format('d/m/Y') }}</div>
                        </div>

                        <div class="col-12">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Deskripsi / Keperluan</label>
                            <div class="p-3 bg-light rounded text-dark" style="white-space: pre-wrap;">{{ $expenseRequest->description }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachments Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-paperclip text-primary me-2"></i>Lampiran Dokumen</h5>
                    
                    @forelse($expenseRequest->attachments as $attachment)
                        <div class="p-3 border rounded d-flex align-items-center gap-3">
                            @php
                                $icon = 'bi-file-earmark-text';
                                if(str_contains($attachment->file_type, 'image')) $icon = 'bi-file-earmark-image';
                                if(str_contains($attachment->file_type, 'pdf')) $icon = 'bi-file-earmark-pdf';
                            @endphp
                            <i class="bi {{ $icon }} fs-2 text-primary"></i>
                            <div>
                                <div class="fw-semibold text-dark">{{ $attachment->file_name }}</div>
                                <small class="text-muted">{{ number_format($attachment->file_size / 1024, 0, ',', '.') }} KB | {{ $attachment->file_type }}</small>
                            </div>
                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="btn btn-premium btn-outline-premium ms-auto">
                                <i class="bi bi-box-arrow-up-right"></i> Lihat Berkas
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted">Tidak ada lampiran dokumen untuk pengajuan ini.</div>
                    @endforelse
                </div>
            </div>

            <!-- Payment Details Card (If Paid) -->
            @if($expenseRequest->status === 'Paid' && $expenseRequest->payment)
                <div class="card border-0 shadow-sm border-start border-4 border-info">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3 text-info"><i class="bi bi-credit-card-2-front me-2"></i>Informasi Pembayaran</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Nomor Pembayaran (Reference No.)</span>
                                <strong class="text-dark fs-5">{{ $expenseRequest->payment->payment_number }}</strong>
                            </div>
                            
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Tanggal Pembayaran</span>
                                <strong class="text-dark">{{ $expenseRequest->payment->payment_date->format('d/m/Y') }}</strong>
                            </div>

                            <div class="col-md-6">
                                <span class="text-muted small d-block">Jumlah Dibayarkan</span>
                                <strong class="text-success fs-5">Rp {{ number_format($expenseRequest->payment->amount, 0, ',', '.') }}</strong>
                            </div>

                            <div class="col-md-6">
                                <span class="text-muted small d-block">Diproses Oleh Finance</span>
                                <strong class="text-dark">{{ $expenseRequest->payment->paidBy->name }}</strong>
                            </div>

                            @if($expenseRequest->payment->notes)
                                <div class="col-12">
                                    <span class="text-muted small d-block">Catatan Pembayaran</span>
                                    <div class="p-2 bg-light rounded text-dark small">{{ $expenseRequest->payment->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Timeline & Actions -->
        <div class="col-lg-4">
            <!-- Actions Block -->
            @if($expenseRequest->status === 'Draft')
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 text-center">
                        <h6 class="fw-semibold text-muted mb-3">Tindakan Pengajuan</h6>
                        <div class="d-grid gap-2">
                            <form action="{{ route('expense-requests.submit', $expenseRequest->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-premium btn-primary-premium w-100 py-3">
                                    <i class="bi bi-send-fill"></i> Kirim untuk Approval
                                </button>
                            </form>
                            
                            <a href="{{ route('expense-requests.edit', $expenseRequest->id) }}" class="btn btn-light w-100 py-2">
                                <i class="bi bi-pencil"></i> Ubah Draft
                            </a>
                            
                            <button class="btn btn-outline-danger w-100 py-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash"></i> Hapus Draft
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body py-3">
                                Apakah Anda yakin ingin menghapus pengajuan draft <strong>{{ $expenseRequest->request_number }}</strong>?
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                <form action="{{ route('expense-requests.destroy', $expenseRequest->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Approval Timeline -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-git me-2 text-primary"></i>Riwayat Persetujuan</h5>
                    
                    @if($expenseRequest->approvalHistories->count() > 0)
                        <div class="position-relative ps-4" style="border-left: 2px solid #e2e8f0;">
                            @foreach($expenseRequest->approvalHistories as $history)
                                <div class="position-relative mb-4">
                                    <!-- Timeline Dot -->
                                    <span class="position-absolute rounded-circle bg-white d-flex align-items-center justify-content-center" style="left: -33px; top: 0; width: 24px; height: 24px; border: 2px solid #0d9488;">
                                        @if($history->action === 'Approve' || $history->action === 'Pay')
                                            <i class="bi bi-check-lg text-success small" style="font-size: 0.75rem;"></i>
                                        @elseif($history->action === 'Reject')
                                            <i class="bi bi-x-lg text-danger small" style="font-size: 0.75rem;"></i>
                                        @else
                                            <i class="bi bi-send text-primary small" style="font-size: 0.75rem;"></i>
                                        @endif
                                    </span>
                                    
                                    <div>
                                        <div class="fw-bold text-dark text-capitalize">{{ $history->action === 'Pay' ? 'Paid' : ($history->action === 'Submit' ? 'Submitted' : $history->action . 'd') }}</div>
                                        <div class="text-muted small">
                                            Oleh: <strong>{{ $history->user->name }}</strong> ({{ $history->role }})
                                        </div>
                                        <div class="text-muted small">{{ $history->created_at->format('d/m/Y H:i') }}</div>
                                        
                                        @if($history->notes)
                                            <div class="mt-2 p-2 bg-light rounded text-dark small italic" style="font-style: italic;">
                                                "{{ $history->notes }}"
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">Belum ada riwayat persetujuan untuk pengajuan ini.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
