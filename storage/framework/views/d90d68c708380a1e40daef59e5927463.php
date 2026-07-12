<?php $__env->startSection('title', 'Laporan Pengeluaran Dana'); ?>
<?php $__env->startSection('page-title', 'Laporan Pengeluaran'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Laporan Pengeluaran</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body p-4">
        <!-- Filter Form -->
        <form method="GET" action="<?php echo e(route('reports.index')); ?>" id="reportFilterForm" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-muted">Cari</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Nomor / Pemohon..." value="<?php echo e(request('search')); ?>">
                </div>
            </div>
            
            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-semibold text-muted">Kategori</label>
                <select name="category_id" class="form-select bg-light">
                    <option value="">Semua Kategori</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-semibold text-muted">Status</label>
                <select name="status" class="form-select bg-light">
                    <option value="">Semua Status</option>
                    <option value="Waiting Supervisor Approval" <?php echo e(request('status') === 'Waiting Supervisor Approval' ? 'selected' : ''); ?>>Waiting Supervisor</option>
                    <option value="Waiting Manager Approval" <?php echo e(request('status') === 'Waiting Manager Approval' ? 'selected' : ''); ?>>Waiting Manager</option>
                    <option value="Waiting Director Approval" <?php echo e(request('status') === 'Waiting Director Approval' ? 'selected' : ''); ?>>Waiting Director</option>
                    <option value="Waiting Finance" <?php echo e(request('status') === 'Waiting Finance' ? 'selected' : ''); ?>>Waiting Finance</option>
                    <option value="Paid" <?php echo e(request('status') === 'Paid' ? 'selected' : ''); ?>>Paid (Selesai Dibayar)</option>
                    <option value="Rejected" <?php echo e(request('status') === 'Rejected' ? 'selected' : ''); ?>>Rejected (Ditolak)</option>
                </select>
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-semibold text-muted">Mulai Tanggal</label>
                <input type="date" name="start_date" class="form-control bg-light" value="<?php echo e(request('start_date')); ?>">
            </div>

            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control bg-light" value="<?php echo e(request('end_date')); ?>">
            </div>

            <div class="col-12 col-md-1 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary-premium w-100 py-2 d-flex align-items-center justify-content-center" title="Terapkan Filter">
                    <i class="bi bi-funnel"></i>
                </button>
                <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-premium w-100 py-2 d-flex align-items-center justify-content-center" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Reports Table Card -->
<div class="card custom-table-card border-0 mb-4">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <h5 class="fw-bold text-dark mb-0">Hasil Analisa Laporan</h5>
        
        <div class="d-inline-flex gap-2">
            <!-- Export to PDF with current filter queries -->
            <a href="<?php echo e(route('reports.export.pdf', request()->query())); ?>" class="btn btn-danger py-2 px-3 border-0 d-flex align-items-center gap-2 btn-export" style="background-color: #ef4444;" title="Ekspor ke PDF">
                <i class="bi bi-file-earmark-pdf-fill"></i> Ekspor PDF
            </a>
            
            <!-- Export to CSV/Excel with current filter queries -->
            <a href="<?php echo e(route('reports.export.csv', request()->query())); ?>" class="btn btn-success py-2 px-3 border-0 d-flex align-items-center gap-2 btn-export" style="background-color: #10b981;" title="Ekspor ke Excel/CSV">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Ekspor Excel
            </a>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table custom-table align-middle">
            <thead>
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Pemohon</th>
                    <th>Kategori</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Tgl Pembayaran</th>
                    <th>No Ref Bank</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-semibold text-dark"><?php echo e($submission->submission_number); ?></td>
                        <td>
                            <div class="fw-semibold text-dark"><?php echo e($submission->user->name); ?></div>
                            <small class="text-muted" style="font-size: 0.75rem;"><?php echo e($submission->user->email); ?></small>
                        </td>
                        <td><?php echo e($submission->category->name); ?></td>
                        <td class="fw-bold text-dark">Rp <?php echo e(number_format($submission->requested_amount, 0, ',', '.')); ?></td>
                        <td>
                            <?php
                                $badgeClass = 'badge-draft';
                                if ($submission->status === 'Waiting Supervisor Approval') $badgeClass = 'badge-waiting-supervisor';
                                elseif ($submission->status === 'Waiting Manager Approval') $badgeClass = 'badge-waiting-manager';
                                elseif ($submission->status === 'Waiting Director Approval') $badgeClass = 'badge-waiting-director';
                                elseif ($submission->status === 'Waiting Finance') $badgeClass = 'badge-waiting-finance';
                                elseif ($submission->status === 'Rejected') $badgeClass = 'badge-rejected';
                                elseif ($submission->status === 'Paid') $badgeClass = 'badge-paid';
                            ?>
                            <span class="badge-status <?php echo e($badgeClass); ?>"><?php echo e($submission->status); ?></span>
                        </td>
                        <td>
                            <?php if($submission->payment): ?>
                                <?php echo e($submission->payment->payment_date->format('d-m-Y')); ?>

                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="font-monospace text-dark small">
                            <?php if($submission->payment): ?>
                                <?php echo e($submission->payment->reference_number); ?>

                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-break fs-2"></i>
                            <p class="mt-2 mb-0">Tidak ada pengajuan transaksi yang cocok dengan kriteria filter.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if($submissions->hasPages()): ?>
        <div class="card-footer bg-white border-0 py-3 px-4">
            <?php echo e($submissions->links('pagination::bootstrap-5')); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\rifki\.gemini\antigravity\scratch\sistem-pengajuan-transaksi\resources\views/reports/index.blade.php ENDPATH**/ ?>