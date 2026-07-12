<?php $__env->startSection('title', 'Daftar Pengajuan Transaksi'); ?>
<?php $__env->startSection('page-title', 'Daftar Pengajuan Transaksi'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb custom-breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengajuan Saya</li>
        </ol>
    </nav>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row g-4 mb-4">
        <!-- Search and Filter Form -->
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4">
                <form method="GET" action="<?php echo e(route('expense-requests.index')); ?>" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="form-label fw-semibold text-muted small">Cari No. Pengajuan / Vendor / Deskripsi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-0" id="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Masukkan kata kunci...">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="status" class="form-label fw-semibold text-muted small">Filter Status</label>
                        <select class="form-select bg-light border-0" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="Draft" <?php echo e(request('status') === 'Draft' ? 'selected' : ''); ?>>Draft</option>
                            <option value="Waiting Supervisor" <?php echo e(request('status') === 'Waiting Supervisor' ? 'selected' : ''); ?>>Waiting Supervisor</option>
                            <option value="Waiting Manager" <?php echo e(request('status') === 'Waiting Manager' ? 'selected' : ''); ?>>Waiting Manager</option>
                            <option value="Waiting Director" <?php echo e(request('status') === 'Waiting Director' ? 'selected' : ''); ?>>Waiting Director</option>
                            <option value="Waiting Finance" <?php echo e(request('status') === 'Waiting Finance' ? 'selected' : ''); ?>>Waiting Finance</option>
                            <option value="Approved" <?php echo e(request('status') === 'Approved' ? 'selected' : ''); ?>>Approved (Ready to Pay)</option>
                            <option value="Rejected" <?php echo e(request('status') === 'Rejected' ? 'selected' : ''); ?>>Rejected</option>
                            <option value="Paid" <?php echo e(request('status') === 'Paid' ? 'selected' : ''); ?>>Paid</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-premium btn-primary-premium flex-grow-1">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <?php if(request()->anyFilled(['search', 'status'])): ?>
                            <a href="<?php echo e(route('expense-requests.index')); ?>" class="btn btn-light" title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Expense Requests Table -->
        <div class="col-12">
            <div class="card custom-table-card">
                <div class="card-header">
                    <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-wallet2 text-primary me-2"></i>Riwayat Pengajuan</h5>
                    <a href="<?php echo e(route('expense-requests.create')); ?>" class="btn btn-premium btn-primary-premium">
                        <i class="bi bi-plus-lg"></i> Tambah Pengajuan
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
                                <th>Urgensi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $expenseRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo e($request->request_number); ?></td>
                                    <td><?php echo e($request->request_date->format('d/m/Y')); ?></td>
                                    <td><?php echo e($request->category->name); ?></td>
                                    <td><?php echo e($request->vendor); ?></td>
                                    <td class="fw-semibold text-primary">Rp <?php echo e(number_format($request->amount, 0, ',', '.')); ?></td>
                                    <td>
                                        <?php
                                            $urgencyBadge = 'bg-secondary';
                                            if($request->urgency === 'High') $urgencyBadge = 'bg-warning text-dark';
                                            if($request->urgency === 'Urgent') $urgencyBadge = 'bg-danger';
                                            if($request->urgency === 'Medium') $urgencyBadge = 'bg-info text-white';
                                        ?>
                                        <span class="badge <?php echo e($urgencyBadge); ?>"><?php echo e($request->urgency); ?></span>
                                    </td>
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
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Aksi
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('expense-requests.show', $request->id)); ?>">
                                                        <i class="bi bi-eye text-muted"></i> Detail
                                                    </a>
                                                </li>
                                                <?php if($request->status === 'Draft'): ?>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('expense-requests.edit', $request->id)); ?>">
                                                            <i class="bi bi-pencil text-muted"></i> Edit Draft
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="<?php echo e(route('expense-requests.submit', $request->id)); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-success">
                                                                <i class="bi bi-send-fill text-success"></i> Submit Pengajuan
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center gap-2 text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo e($request->id); ?>">
                                                            <i class="bi bi-trash text-danger"></i> Hapus Draft
                                                        </button>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>

                                        <!-- Delete Modal -->
                                        <?php if($request->status === 'Draft'): ?>
                                            <div class="modal fade" id="deleteModal<?php echo e($request->id); ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo e($request->id); ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0">
                                                        <div class="modal-header border-0 pb-0">
                                                            <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel<?php echo e($request->id); ?>"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body py-3">
                                                            Apakah Anda yakin ingin menghapus pengajuan draft <strong><?php echo e($request->request_number); ?></strong>? Tindakan ini tidak dapat dibatalkan.
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <form action="<?php echo e(route('expense-requests.destroy', $request->id)); ?>" method="POST">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('DELETE'); ?>
                                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 mb-2 d-block"></i>
                                        Belum ada pengajuan transaksi pengeluaran.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($expenseRequests->hasPages()): ?>
                    <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                        <?php echo e($expenseRequests->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\rifki\.gemini\antigravity\scratch\sistem-pengajuan-transaksi\resources\views/expense-requests/index.blade.php ENDPATH**/ ?>