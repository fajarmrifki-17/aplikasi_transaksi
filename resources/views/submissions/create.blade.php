@extends('layouts.app')

@section('title', 'Buat Pengajuan Transaksi')
@section('page-title', 'Buat Pengajuan Baru')

@section('breadcrumbs')
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('submissions.index') }}">Pengajuan Saya</a></li>
    <li class="breadcrumb-item active" aria-current="page">Buat Baru</li>
</ol>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-plus text-primary me-2"></i>Form Pengajuan Transaksi</h5>
            </div>
            
            <div class="card-body p-4">
                <form id="submissionForm" method="POST" action="{{ route('submissions.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Category Field -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold text-muted small">Kategori Pengeluaran <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select bg-light @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Requested Amount Field -->
                    <div class="mb-3">
                        <label for="requested_amount_display" class="form-label fw-semibold text-muted small">Nominal Pengajuan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-secondary-subtle border-0 text-dark fw-bold">Rp</span>
                            <input type="text" id="requested_amount_display" class="form-control bg-light border-0 fw-bold fs-5 text-dark" placeholder="0" required>
                            <input type="hidden" name="requested_amount" id="requested_amount" value="{{ old('requested_amount') }}">
                        </div>
                        <small class="text-muted" id="amount_formatted_word"></small>
                        @error('requested_amount')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description Field -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold text-muted small">Deskripsi Keperluan <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control bg-light @error('description') is-invalid @enderror" rows="5" placeholder="Tuliskan detail keperluan pengeluaran..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Attachments Upload Field -->
                    <div class="mb-4">
                        <label for="attachments" class="form-label fw-semibold text-muted small">Berkas Pendukung (Multiple Upload) <span class="text-muted">(Opsional)</span></label>
                        <input type="file" name="attachments[]" id="attachments" class="form-control bg-light @error('attachments') is-invalid @enderror" multiple>
                        <div class="form-text text-muted small mt-1">
                            <i class="bi bi-info-circle me-1"></i>Format yang didukung: <strong>PDF, JPG, JPEG, PNG</strong>. Maksimal <strong>5 MB</strong> per berkas.
                        </div>
                        <div id="fileList" class="mt-2 d-flex flex-column gap-1"></div>
                        @error('attachments')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('submissions.index') }}" class="btn btn-outline-premium">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary-premium">
                            <i class="bi bi-save me-1"></i>Simpan Draft
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const displayInput = document.getElementById('requested_amount_display');
        const hiddenInput = document.getElementById('requested_amount');
        const fileInput = document.getElementById('attachments');
        const fileList = document.getElementById('fileList');

        // Initial loading of old values if exist
        if (hiddenInput.value) {
            displayInput.value = formatRupiah(hiddenInput.value);
        }

        // Format raw input values as Indonesian Rupiah currency format
        displayInput.addEventListener('keyup', function(e) {
            let val = this.value.replace(/[^0-9]/g, '');
            if (val) {
                this.value = formatRupiah(val);
                hiddenInput.value = val;
            } else {
                this.value = '';
                hiddenInput.value = '';
            }
        });

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Client side validation for files
        fileInput.addEventListener('change', function(e) {
            fileList.innerHTML = '';
            const files = Array.from(this.files);
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            const maxSize = 5 * 1024 * 1024; // 5 MB

            let valid = true;

            files.forEach(file => {
                const isTypeValid = allowedTypes.includes(file.type);
                const isSizeValid = file.size <= maxSize;

                let textClass = 'text-success';
                let icon = 'bi-check-circle-fill';
                let errorMsg = '';

                if (!isTypeValid || !isSizeValid) {
                    textClass = 'text-danger';
                    icon = 'bi-x-circle-fill';
                    valid = false;

                    if (!isTypeValid) errorMsg = ' (Format tidak valid)';
                    else if (!isSizeValid) errorMsg = ' (Melebihi 5MB)';
                }

                fileList.innerHTML += `
                    <div class="d-flex align-items-center gap-2 small ${textClass}">
                        <i class="bi ${icon}"></i>
                        <span>${file.name} - ${(file.size / (1024 * 1024)).toFixed(2)} MB${errorMsg}</span>
                    </div>
                `;
            });

            if (!valid) {
                Swal.fire({
                    title: 'Berkas Tidak Valid!',
                    text: 'Pastikan seluruh berkas pendukung Anda berformat PDF/JPG/JPEG/PNG dan berukuran di bawah 5 MB.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
                fileInput.value = ''; // Reset files
            }
        });
    });
</script>
@endsection
