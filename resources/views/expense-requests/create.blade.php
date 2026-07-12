@extends('layouts.app')

@section('title', 'Buat Pengajuan Baru')
@section('page-title', 'Buat Pengajuan Transaksi')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb custom-breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('expense-requests.index') }}">Pengajuan Saya</a></li>
            <li class="breadcrumb-item active" aria-current="page">Buat Pengajuan</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm p-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-4 text-dark"><i class="bi bi-file-earmark-plus text-primary me-2"></i>Form Pengajuan Transaksi</h5>
                    
                    <form action="{{ route('expense-requests.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Category -->
                            <div class="col-md-6">
                                <label for="expense_category_id" class="form-label fw-semibold">Kategori Pengeluaran <span class="text-danger">*</span></label>
                                <select class="form-select @error('expense_category_id') is-invalid @enderror" id="expense_category_id" name="expense_category_id">
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                            [{{ $category->code }}] {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('expense_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div class="col-md-6">
                                <label for="amount" class="form-label fw-semibold">Nominal Pengeluaran (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" step="0.01" min="1" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Contoh: 500000">
                                    @error('amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Vendor -->
                            <div class="col-md-6">
                                <label for="vendor" class="form-label fw-semibold">Vendor / Toko / Penerima <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('vendor') is-invalid @enderror" id="vendor" name="vendor" value="{{ old('vendor') }}" placeholder="Contoh: PT. Sumber Makmur atau Toko Buku Jaya">
                                @error('vendor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Urgency -->
                            <div class="col-md-3">
                                <label for="urgency" class="form-label fw-semibold">Urgensi <span class="text-danger">*</span></label>
                                <select class="form-select @error('urgency') is-invalid @enderror" id="urgency" name="urgency">
                                    <option value="Low" {{ old('urgency') == 'Low' ? 'selected' : '' }}>Low</option>
                                    <option value="Medium" {{ old('urgency') == 'Medium' || !old('urgency') ? 'selected' : '' }}>Medium</option>
                                    <option value="High" {{ old('urgency') == 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Urgent" {{ old('urgency') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('urgency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Needed Date -->
                            <div class="col-md-3">
                                <label for="needed_date" class="form-label fw-semibold">Tanggal Dibutuhkan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('needed_date') is-invalid @enderror" id="needed_date" name="needed_date" value="{{ old('needed_date') }}">
                                @error('needed_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Deskripsi Pengajuan / Keperluan <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Tuliskan tujuan pengeluaran ini secara mendetail...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- File Attachment -->
                            <div class="col-12">
                                <label for="attachment" class="form-label fw-semibold">Lampiran Dokumen Pendukung <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment">
                                <div class="form-text text-muted small mt-1">Format file yang didukung: PDF, JPG, JPEG, PNG (Maksimal 5 MB)</div>
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit / Cancel Buttons -->
                            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('expense-requests.index') }}" class="btn btn-outline-premium">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-premium btn-primary-premium">
                                    <i class="bi bi-save"></i> Simpan Draft
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
