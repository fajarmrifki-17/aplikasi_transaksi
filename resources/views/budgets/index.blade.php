@extends('layouts.app')

@section('title', 'Alokasi Anggaran Kategori')
@section('page-title', 'Alokasi Anggaran Kategori')

@section('breadcrumbs')
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Alokasi Anggaran</li>
</ol>
@endsection

@section('content')
<!-- Budgets Table Card -->
<div class="card custom-table-card border-0">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">Daftar Alokasi Anggaran</h5>
        @if(Auth::user()->isFinance())
            <button class="btn btn-primary-premium" data-bs-toggle="modal" data-bs-target="#createBudgetModal">
                <i class="bi bi-cash-coin"></i> Alokasi Anggaran Baru
            </button>
        @endif
    </div>
    
    <div class="table-responsive">
        <table class="table custom-table align-middle">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Tahun Fiskal</th>
                    <th>Limit Anggaran</th>
                    <th>Terpakai</th>
                    <th>Sisa Saldo</th>
                    <th style="width: 20%;">Penggunaan</th>
                    @if(Auth::user()->isFinance())
                        <th class="text-end">Tindakan</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($budgets as $budget)
                    @php
                        $percentage = $budget->limit_amount > 0 ? ($budget->spent_amount / $budget->limit_amount) * 100 : 0;
                        $progressColor = 'bg-success';
                        if ($percentage > 70 && $percentage <= 90) $progressColor = 'bg-warning';
                        elseif ($percentage > 90) $progressColor = 'bg-danger';
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $budget->category->name }}</div>
                            <small class="text-muted font-monospace text-uppercase" style="font-size: 0.65rem;">{{ $budget->category->code }}</small>
                        </td>
                        <td class="fw-bold text-dark">{{ $budget->fiscal_year }}</td>
                        <td class="fw-semibold text-dark">Rp {{ number_format($budget->limit_amount, 0, ',', '.') }}</td>
                        <td class="text-warning-emphasis">Rp {{ number_format($budget->spent_amount, 0, ',', '.') }}</td>
                        <td class="fw-bold text-success-emphasis">Rp {{ number_format($budget->remaining_amount, 0, ',', '.') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="small fw-semibold text-dark">{{ number_format($percentage, 0) }}%</span>
                            </div>
                        </td>
                        @if(Auth::user()->isFinance())
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Edit Button (trigger Modal) -->
                                    <button class="btn btn-sm btn-warning text-white px-2 py-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editBudgetModal{{ $budget->id }}" 
                                            title="Ubah Limit Anggaran">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form method="POST" action="{{ route('budgets.destroy', $budget) }}" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger px-2 py-1 btn-delete-budget" title="Hapus Anggaran">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>

                    <!-- Edit Budget Modal for this specific item -->
                    @if(Auth::user()->isFinance())
                        <div class="modal fade" id="editBudgetModal{{ $budget->id }}" tabindex="-1" aria-labelledby="editBudgetModalLabel{{ $budget->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('budgets.update', $budget) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold" id="editBudgetModalLabel{{ $budget->id }}">Ubah Limit Anggaran</h5>
                                            <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#editBudgetModal{{ $budget->id }}" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-muted">Kategori</label>
                                                <input type="text" class="form-control bg-light" value="{{ $budget->category->name }}" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-muted">Tahun Fiskal</label>
                                                <input type="text" class="form-control bg-light" value="{{ $budget->fiscal_year }}" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-muted">Dana Terpakai Saat Ini</label>
                                                <input type="text" class="form-control bg-light" value="Rp {{ number_format($budget->spent_amount, 0, ',', '.') }}" disabled>
                                            </div>
                                            <!-- Limit Amount -->
                                            <div class="mb-3">
                                                <label for="limit_amount{{ $budget->id }}" class="form-label small fw-semibold text-muted">Limit Anggaran Baru <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" name="limit_amount" id="limit_amount{{ $budget->id }}" class="form-control bg-light" value="{{ old('limit_amount', (int)$budget->limit_amount) }}" min="{{ (int)$budget->spent_amount }}" required>
                                                </div>
                                                <div class="form-text text-muted small mt-1">Limit baru tidak boleh lebih kecil dari dana yang telah terpakai.</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary-premium">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-wallet-fill fs-2"></i>
                            <p class="mt-2 mb-0">Belum ada alokasi anggaran kategori dibuat.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($budgets->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-4">
            {{ $budgets->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Create Budget Modal -->
@if(Auth::user()->isFinance())
    <div class="modal fade" id="createBudgetModal" tabindex="-1" aria-labelledby="createBudgetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('budgets.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="createBudgetModalLabel">Alokasi Anggaran Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Category Select -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label small fw-semibold text-muted">Pilih Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select bg-light" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Fiscal Year -->
                        <div class="mb-3">
                            <label for="fiscal_year" class="form-label small fw-semibold text-muted">Tahun Fiskal <span class="text-danger">*</span></label>
                            <input type="number" name="fiscal_year" id="fiscal_year" class="form-control bg-light" value="{{ old('fiscal_year', date('Y')) }}" min="2020" max="2100" required>
                        </div>
                        <!-- Limit Amount -->
                        <div class="mb-3">
                            <label for="limit_amount" class="form-label small fw-semibold text-muted">Limit Alokasi Dana <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="limit_amount" id="limit_amount" class="form-control bg-light" placeholder="0" value="{{ old('limit_amount') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-premium">Alokasikan Dana</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Confirmation for delete
        const deleteButtons = document.querySelectorAll('.btn-delete-budget');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.delete-form');
                Swal.fire({
                    title: 'Hapus Alokasi Anggaran?',
                    text: 'Alokasi anggaran ini akan dihapus permanen. Tindakan ini akan gagal jika ada dana terpakai.',
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
    });
</script>
@endsection
