<?php $__env->startSection('title', 'Validasi & Pembayaran Transaksi'); ?>
<?php $__env->startSection('page-title', 'Validasi & Pembayaran'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb custom-breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pembayaran</li>
        </ol>
    </nav>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Validation Queue -->
    <div class="card custom-table-card border-start border-4 border-warning mb-5">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-shield-exclamation text-warning me-2"></i>Antrean Validasi Anggaran & Saldo</h5>
            <span class="badge bg-warning text-dark"><?php echo e(count($waitingValidation)); ?> Pengajuan</span>
        </div>
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Tanggal</th>
                        <th>Pengaju</th>
                        <th>Departemen</th>
                        <th>Kategori</th>
                        <th>Nominal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $waitingValidation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="fw-semibold"><?php echo e($request->request_number); ?></td>
                            <td><?php echo e($request->request_date->format('d/m/Y')); ?></td>
                            <td><?php echo e($request->user->name); ?></td>
                            <td><?php echo e($request->user->department->code ?? '-'); ?></td>
                            <td><?php echo e($request->category->name); ?></td>
                            <td class="fw-semibold text-primary">Rp <?php echo e(number_format($request->amount, 0, ',', '.')); ?></td>
                            <td>
                                <a href="<?php echo e(route('payments.show', $request->id)); ?>" class="btn btn-sm btn-premium btn-primary-premium">
                                    <i class="bi bi-shield-check"></i> Validasi
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada pengajuan menunggu validasi anggaran saat ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ready to Pay Queue -->
    <div class="card custom-table-card border-start border-4 border-primary mb-5">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-cash-stack text-primary me-2"></i>Siap Dibayar (Sudah Divalidasi)</h5>
            <span class="badge bg-primary"><?php echo e(count($waitingPayment)); ?> Pengajuan</span>
        </div>
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Tanggal</th>
                        <th>Pengaju</th>
                        <th>Departemen</th>
                        <th>Kategori</th>
                        <th>Nominal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $waitingPayment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="fw-semibold"><?php echo e($request->request_number); ?></td>
                            <td><?php echo e($request->request_date->format('d/m/Y')); ?></td>
                            <td><?php echo e($request->user->name); ?></td>
                            <td><?php echo e($request->user->department->code ?? '-'); ?></td>
                            <td><?php echo e($request->category->name); ?></td>
                            <td class="fw-semibold text-primary">Rp <?php echo e(number_format($request->amount, 0, ',', '.')); ?></td>
                            <td>
                                <a href="<?php echo e(route('payments.show', $request->id)); ?>" class="btn btn-sm btn-success">
                                    <i class="bi bi-credit-card"></i> Proses Bayar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada pengajuan yang siap dibayar saat ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paid History Table -->
    <div class="card custom-table-card">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-clock-history text-muted me-2"></i>Riwayat Pembayaran Selesai</h5>
        </div>
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>No. Pembayaran</th>
                        <th>No. Pengajuan</th>
                        <th>Tanggal Bayar</th>
                        <th>Kategori</th>
                        <th>Vendor</th>
                        <th>Nominal</th>
                        <th>Petugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="fw-semibold text-info"><?php echo e($payment->payment_number); ?></td>
                            <td><?php echo e($payment->expenseRequest->request_number); ?></td>
                            <td><?php echo e($payment->payment_date->format('d/m/Y')); ?></td>
                            <td><?php echo e($payment->expenseRequest->category->name); ?></td>
                            <td><?php echo e($payment->expenseRequest->vendor); ?></td>
                            <td class="fw-semibold">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></td>
                            <td><?php echo e($payment->paidBy->name); ?></td>
                            <td>
                                <a href="<?php echo e(route('payments.show', $payment->expenseRequest->id)); ?>" class="btn btn-sm btn-light">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada pengeluaran yang terbayar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($payments->hasPages()): ?>
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                <?php echo e($payments->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\rifki\.gemini\antigravity\scratch\sistem-pengajuan-transaksi\resources\views/payments/index.blade.php ENDPATH**/ ?>