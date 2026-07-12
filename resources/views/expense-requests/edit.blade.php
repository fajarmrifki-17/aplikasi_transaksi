@extends('layouts.app')

@section('title', 'Ubah Pengajuan')
@section('page-title', 'Ubah Pengajuan Transaksi')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb custom-breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('expense-requests.index') }}">Pengajuan Saya</a></li>
            <li class="breadcrumb-item"><a href="{{ route('expense-requests.show', $expenseRequest->id) }}">{{ $expenseRequest->request_number }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ubah</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm p-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-4 text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Form Ubah Pengajuan Draft</h5>
                    
                    <form action="{{ route('expense-requests.update', $expenseRequest->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- Request Number (Disabled) -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Nomor Pengajuan</label>
                                <input type="text" class="form-control bg-light border-0" value="{{ $expenseRequest->request_number }}" disabled>
                            </div>

                            <!-- Date (Disabled) -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Tanggal Pengajuan</label>
                                <input type="text" class="form-control bg-light border-0" value="{{ $expenseRequest->request_date->format('d/m/Y') }}" disabled>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <label for="expense_category_id" class="form-label fw-semibold">Kategori Pengeluaran <span class="text-danger">*</span></label>
                                <select class="form-select @error('expense_category_id') is-invalid @enderror" id="expense_category_id" name="expense_category_id">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('expense_category_id', $expenseRequest->expense_category_id) == $category->id ? 'selected' : '' }}>
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
                                    <input type="number" step="0.01" min="1" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $expenseRequest->amount) }}">
                                    @error('amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Vendor -->
                            <div class="col-md-6">
                                <label for="vendor" class="form-label fw-semibold">Vendor / Toko / Penerima <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('vendor') is-invalid @enderror" id="vendor" name="vendor" value="{{ old('vendor', $expenseRequest->vendor) }}">
                                @error('vendor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Urgency -->
                            <div class="col-md-3">
                                <label for="urgency" class="form-label fw-semibold">Urgensi <span class="text-danger">*</span></label>
                                <select class="form-select @error('urgency') is-invalid @enderror" id="urgency" name="urgency">
                                    <option value="Low" {{ old('urgency', $expenseRequest->urgency) == 'Low' ? 'selected' : '' }}>Low</option>
                                    <option value="Medium" {{ old('urgency', $expenseRequest->urgency) == 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="High" {{ old('urgency', $expenseRequest->urgency) == 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Urgent" {{ old('urgency', $expenseRequest->urgency) == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('urgency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Needed Date -->
                            <div class="col-md-3">
                                <label for="needed_date" class="form-label fw-semibold">Tanggal Dibutuhkan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('needed_date') is-invalid @enderror" id="needed_date" name="needed_date" value="{{ old('needed_date', $expenseRequest->needed_date->format('Y-m-d')) }}">
                                @error('needed_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Deskripsi Pengajuan / Keperluan <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $expenseRequest->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- File Attachment -->
                            <div class="col-12">
                                <label for="attachment" class="form-label fw-semibold">Lampiran Dokumen Pendukung</label>
                                <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment">
                                <div class="form-text text-muted small mt-1">Biarkan kosong jika tidak ingin mengganti lampiran saat ini. Format didukung: PDF, JPG, JPEG, PNG (Maks 5 MB)</div>
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if($expenseRequest->attachments->count() > 0)
                                    <div class="mt-3 p-3 bg-light rounded d-flex align-items-center gap-3">
                                        <i class="bi bi-file-earmark-check fs-2 text-success"></i>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $expenseRequest->attachments->first()->file_name }}</div>
                                            <small class="text-muted">{{ number_format($expenseRequest->attachments->first()->file_size / 1024, 0, ',', '.') }} KB | {{ $expenseRequest->attachments->first()->file_type }}</small>
                                        </div>
                                        <a href="{{ asset('storage/' . $expenseRequest->attachments->first()->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary ms-auto">
                                            <i class="bi bi-box-arrow-up-right"></i> Lihat Berkas
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Submit / Cancel Buttons -->
                            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('expense-requests.show', $expenseRequest->id) }}" class="btn btn-outline-premium">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-premium btn-primary-premium">
                                    <i class="bi bi-save"></i> Perbarui Draft
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
