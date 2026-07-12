@extends('layouts.app')

@section('title', 'Kategori Pengeluaran')
@section('page-title', 'Kategori Pengeluaran')

@section('breadcrumbs')
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Kategori Pengeluaran</li>
</ol>
@endsection

@section('content')
<!-- Categories Table Card -->
<div class="card custom-table-card border-0">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">Daftar Kategori</h5>
        @if(Auth::user()->isFinance())
            <button class="btn btn-primary-premium" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-lg"></i> Tambah Kategori
            </button>
        @endif
    </div>
    
    <div class="table-responsive">
        <table class="table custom-table align-middle">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-center">Total Pengajuan</th>
                    @if(Auth::user()->isFinance())
                        <th class="text-end">Tindakan</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td><span class="badge bg-secondary text-white font-monospace">{{ $category->code }}</span></td>
                        <td class="fw-semibold text-dark">{{ $category->name }}</td>
                        <td class="text-muted small text-wrap" style="max-width: 300px;">{{ $category->description ?? '-' }}</td>
                        <td class="text-center fw-bold text-dark">{{ $category->submissions_count }}</td>
                        @if(Auth::user()->isFinance())
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Edit Button (trigger Modal) -->
                                    <button class="btn btn-sm btn-warning text-white px-2 py-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCategoryModal{{ $category->id }}" 
                                            title="Ubah Kategori">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger px-2 py-1 btn-delete-category" title="Hapus Kategori">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>

                    <!-- Edit Category Modal for this specific item -->
                    @if(Auth::user()->isFinance())
                        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-labelledby="editCategoryModalLabel{{ $category->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('categories.update', $category) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold" id="editCategoryModalLabel{{ $category->id }}">Ubah Kategori</h5>
                                            <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Code -->
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-muted">Kode Kategori</label>
                                                <input type="text" name="code" class="form-control bg-light" value="{{ old('code', $category->code) }}" required>
                                            </div>
                                            <!-- Name -->
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-muted">Nama Kategori</label>
                                                <input type="text" name="name" class="form-control bg-light" value="{{ old('name', $category->name) }}" required>
                                            </div>
                                            <!-- Description -->
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-muted">Deskripsi</label>
                                                <textarea name="description" class="form-control bg-light" rows="3">{{ old('description', $category->description) }}</textarea>
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
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-tags fs-2"></i>
                            <p class="mt-2 mb-0">Belum ada kategori pengeluaran terdaftar.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-4">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Create Category Modal -->
@if(Auth::user()->isFinance())
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="createCategoryModalLabel">Tambah Kategori Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Code -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Kode Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control bg-light" placeholder="Contoh: OPERASIONAL" value="{{ old('code') }}" required>
                        </div>
                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light" placeholder="Contoh: Operasional Kantor" value="{{ old('name') }}" required>
                        </div>
                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Deskripsi</label>
                            <textarea name="description" class="form-control bg-light" placeholder="Tuliskan keterangan detail kategori pengeluaran..." rows="3">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-premium">Simpan Kategori</button>
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
        const deleteButtons = document.querySelectorAll('.btn-delete-category');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.delete-form');
                Swal.fire({
                    title: 'Hapus Kategori?',
                    text: 'Kategori ini akan dihapus permanen. Tindakan ini akan gagal jika ada relasi anggaran atau pengajuan.',
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
