<?php $__env->startSection('title', 'Persetujuan Transaksi'); ?>
<?php $__env->startSection('page-title', 'Persetujuan Pengajuan'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Antrian Approval</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body p-4">
        <!-- Filter Form -->
        <form method="GET" action="<?php echo e(route('approvals.index')); ?>" class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-muted">Cari</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Nomor / Pemohon..." value="<?php echo e(request('search')); ?>">
                </div>
            </div>
            
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-semibold text-muted">Kategori</label>
                <select name="category_id" class="form-select bg-light">
                    <option value="">Semua Kategori</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-semibold text-muted">Mulai Tanggal</label>
                <input type="date" name="start_date" class="form-control bg-light" value="<?php echo e(request('start_date')); ?>">
            </div>

            <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary-premium w-100 py-2 d-flex align-items-center justify-content-center" title="Terapkan Filter">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="<?php echo e(route('approvals.index')); ?>" class="btn btn-outline-premium w-100 py-2 d-flex align-items-center justify-content-center" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Approvals Table Card -->
<div class="card custom-table-card border-0">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h5 class="fw-bold text-dark mb-0">Antrian Persetujuan (Role: <?php echo e(Auth::user()->roles->first()->name ?? 'N/A'); ?>)</h5>
    </div>
    
    <div class="table-responsive">
        <table class="table custom-table align-middle">
            <thead>
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Pemohon</th>
                    <th>Kategori</th>
                    <th>Nominal</th>
                    <th>Tanggal Pengajuan</th>
                    <th class="text-end">Tindakan</th>
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
                        <td>
                            <span class="fw-medium"><?php echo e($submission->category->name); ?></span>
                        </td>
                        <td class="fw-bold text-primary">Rp <?php echo e(number_format($submission->requested_amount, 0, ',', '.')); ?></td>
                        <td><?php echo e($submission->submission_date->format('d F Y')); ?></td>
                        <td class="text-end">
                            <a href="<?php echo e(route('approvals.show', $submission)); ?>" class="btn btn-sm btn-primary-premium py-1 px-3 border-0">
                                <i class="bi bi-shield-check"></i> Proses Approval
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-patch-check fs-2 text-success"></i>
                            <p class="mt-2 mb-0 fw-semibold text-dark">Antrian Bersih!</p>
                            <p class="text-muted small">Tidak ada pengajuan transaksi yang menunggu approval Anda saat ini.</p>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\rifki\.gemini\antigravity\scratch\sistem-pengajuan-transaksi\resources\views/approvals/index.blade.php ENDPATH**/ ?>