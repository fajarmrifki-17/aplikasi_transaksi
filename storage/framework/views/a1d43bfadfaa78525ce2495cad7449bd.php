<?php $__env->startSection('title', 'Dashboard Staff'); ?>
<?php $__env->startSection('page-title', 'Dashboard Staff'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb custom-breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4 col-xl-2">
            <div class="card card-stat bg-white h-100">
                <div class="card-body">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Total Pengajuan</h6>
                        <h3 class="mb-0 fw-bold"><?php echo e($stats['total']); ?></h3>
                    </div>
                    <div class="stat-icon bg-secondary-subtle text-secondary">
                        <i class="bi bi-folder-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-xl-2">
            <div class="card card-stat bg-white h-100">
                <div class="card-body">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Draft</h6>
                        <h3 class="mb-0 fw-bold"><?php echo e($stats['draft']); ?></h3>
                    </div>
                    <div class="stat-icon bg-light text-dark">
                        <i class="bi bi-file-earmark"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-2">
            <div class="card card-stat bg-white h-100">
                <div class="card-body">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Menunggu</h6>
                        <h3 class="mb-0 fw-bold"><?php echo e($stats['waiting']); ?></h3>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-2">
            <div class="card card-stat bg-white h-100">
                <div class="card-body">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Disetujui</h6>
                        <h3 class="mb-0 fw-bold"><?php echo e($stats['approved']); ?></h3>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-2">
            <div class="card card-stat bg-white h-100">
                <div class="card-body">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Ditolak</h6>
                        <h3 class="mb-0 fw-bold"><?php echo e($stats['rejected']); ?></h3>
                    </div>
                    <div class="stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-2">
            <div class="card card-stat bg-white h-100">
                <div class="card-body">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small fw-bold">Paid</h6>
                        <h3 class="mb-0 fw-bold"><?php echo e($stats['paid']); ?></h3>
                    </div>
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Requests -->
    <div class="card custom-table-card">
        <div class="card-header">
            <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Pengajuan Terbaru</h5>
            <a href="<?php echo e(route('expense-requests.create')); ?>" class="btn btn-premium btn-primary-premium">
                <i class="bi bi-plus-lg"></i> Buat Pengajuan
            </a>
        </div>
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Vendor</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="fw-semibold"><?php echo e($request->request_number); ?></td>
                            <td><?php echo e($request->request_date->format('d/m/Y')); ?></td>
                            <td><?php echo e($request->category->name); ?></td>
                            <td><?php echo e($request->vendor); ?></td>
                            <td class="fw-semibold">Rp <?php echo e(number_format($request->amount, 0, ',', '.')); ?></td>
                            <td>
                                <?php
                                    $badgeClass = 'badge-draft';
                                    switch($request->status) {
                                        case 'Waiting Supervisor': $badgeClass = 'badge-waiting-supervisor'; break;
                                        case 'Waiting Manager': $badgeClass = 'badge-waiting-manager'; break;
                                        case 'Waiting Director': $badgeClass = 'badge-waiting-director'; break;
                                        case 'Waiting Finance': $badgeClass = 'badge-waiting-finance'; break;
                                        case 'Approved': $badgeClass = 'badge-approved'; break;
                                        case 'Rejected': $badgeClass = 'badge-rejected'; break;
                                        case 'Paid': $badgeClass = 'badge-paid'; break;
                                    }
                                ?>
                                <span class="badge-status <?php echo e($badgeClass); ?>"><?php echo e($request->status); ?></span>
                            </td>
                            <td>
                                <a href="<?php echo e(route('expense-requests.show', $request->id)); ?>" class="btn btn-sm btn-light">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada pengajuan transaksi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\rifki\.gemini\antigravity\scratch\sistem-pengajuan-transaksi\resources\views/dashboard/staff.blade.php ENDPATH**/ ?>