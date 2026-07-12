<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Stats Row -->
<div class="row g-4 mb-4">
    <!-- Total Submission -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-stat h-100 border-start border-4 border-primary">
            <div class="card-body">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Total Pengajuan</h6>
                    <h3 class="mb-0 fw-bold text-dark"><?php echo e($stats['total']); ?></h3>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending Approval -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-stat h-100 border-start border-4 border-warning">
            <div class="card-body">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Menunggu Persetujuan</h6>
                    <h3 class="mb-0 fw-bold text-dark"><?php echo e($stats['pending']); ?></h3>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Approved / Waiting Finance -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-stat h-100 border-start border-4 border-success">
            <div class="card-body">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Disetujui</h6>
                    <h3 class="mb-0 fw-bold text-dark"><?php echo e($stats['approved']); ?></h3>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-check-all"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejected -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-stat h-100 border-start border-4 border-danger">
            <div class="card-body">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Ditolak</h6>
                    <h3 class="mb-0 fw-bold text-dark"><?php echo e($stats['rejected']); ?></h3>
                </div>
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart: Monthly Statistics -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold text-dark mb-0">Statistik Transaksi Bulanan (<?php echo e(date('Y')); ?>)</h5>
                <i class="bi bi-calendar3 text-muted"></i>
            </div>
            <div class="card-body px-4 pb-4">
                <div style="height: 300px; width: 100%;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Status Progress bars -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold text-dark mb-0">Sisa Anggaran Kategori (<?php echo e(date('Y')); ?>)</h5>
                <i class="bi bi-cash-coin text-muted"></i>
            </div>
            <div class="card-body px-4 pb-4">
                <?php $__empty_1 = true; $__currentLoopData = $budgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $budget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $percentage = $budget->limit_amount > 0 ? ($budget->spent_amount / $budget->limit_amount) * 100 : 0;
                        $progressColor = 'bg-success';
                        if ($percentage > 70 && $percentage <= 90) $progressColor = 'bg-warning';
                        elseif ($percentage > 90) $progressColor = 'bg-danger';
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="fw-semibold text-dark"><?php echo e($budget->category->name); ?></span>
                            <span class="text-muted"><?php echo e(number_format(100 - $percentage, 1)); ?>% Sisa</span>
                        </div>
                        <div class="progress mb-1" style="height: 8px;">
                            <div class="progress-bar <?php echo e($progressColor); ?>" role="progressbar" style="width: <?php echo e($percentage); ?>%" aria-valuenow="<?php echo e($percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                            <span>Terpakai: Rp <?php echo e(number_format($budget->spent_amount, 0, ',', '.')); ?></span>
                            <span>Limit: Rp <?php echo e(number_format($budget->limit_amount, 0, ',', '.')); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-exclamation-octagon fs-2"></i>
                        <p class="mt-2 mb-0">Tidak ada alokasi anggaran aktif tahun ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Role-Specific Action Table Card -->
    <div class="col-12 col-lg-7">
        <!-- Staff Dashboard Panel -->
        <?php if(Auth::user()->isStaff()): ?>
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold text-dark mb-0">Pengajuan Terakhir Saya</h5>
                    <a href="<?php echo e(route('submissions.create')); ?>" class="btn btn-sm btn-primary-premium">
                        <i class="bi bi-plus-lg"></i> Buat Pengajuan
                    </a>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover small">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Pengajuan</th>
                                    <th>Kategori</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentSubmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?php echo e($submission->submission_number); ?></td>
                                        <td><?php echo e($submission->category->name); ?></td>
                                        <td>Rp <?php echo e(number_format($submission->requested_amount, 0, ',', '.')); ?></td>
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
                                            <a href="<?php echo e(route('submissions.show', $submission)); ?>" class="btn btn-sm btn-outline-secondary py-1 px-2 border-0">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Belum ada pengajuan transaksi dibuat.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Approver Dashboard Panel (Supervisor, Manager, Director) -->
        <?php if(Auth::user()->isSupervisor() || Auth::user()->isManager() || Auth::user()->isDirector()): ?>
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold text-dark mb-0">Menunggu Persetujuan Anda</h5>
                    <a href="<?php echo e(route('approvals.index')); ?>" class="btn btn-sm btn-outline-premium">
                        Lihat Semua <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover small">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Pengajuan</th>
                                    <th>Pemohon</th>
                                    <th>Nominal</th>
                                    <th>Tanggal</th>
                                    <th>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $pendingApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?php echo e($sub->submission_number); ?></td>
                                        <td><?php echo e($sub->user->name); ?></td>
                                        <td>Rp <?php echo e(number_format($sub->requested_amount, 0, ',', '.')); ?></td>
                                        <td><?php echo e($sub->submission_date->format('d-m-Y')); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('approvals.show', $sub)); ?>" class="btn btn-sm btn-primary-premium py-1 px-2 border-0">
                                                <i class="bi bi-shield-check"></i> Proses
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-check-circle text-success fs-3"></i>
                                            <p class="mt-2 mb-0">Bagus! Tidak ada transaksi menunggu approval Anda saat ini.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Finance Dashboard Panel -->
        <?php if(Auth::user()->isFinance()): ?>
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold text-dark mb-0">Antrian Validasi & Bayar (Finance)</h5>
                    <a href="<?php echo e(route('payments.index')); ?>" class="btn btn-sm btn-outline-premium">
                        Lihat Antrian <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover small">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Pengajuan</th>
                                    <th>Pemohon</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $pendingPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?php echo e($sub->submission_number); ?></td>
                                        <td><?php echo e($sub->user->name); ?></td>
                                        <td>Rp <?php echo e(number_format($sub->requested_amount, 0, ',', '.')); ?></td>
                                        <td>
                                            <span class="badge-status badge-waiting-finance">Waiting Finance</span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('payments.show', $sub)); ?>" class="btn btn-sm btn-success py-1 px-2 border-0" style="font-weight: 500;">
                                                <i class="bi bi-cash"></i> Bayar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-check-circle text-success fs-3"></i>
                                            <p class="mt-2 mb-0">Tidak ada antrian pembayaran menunggu diproses.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Audit Trail / Recent Activities Card -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold text-dark mb-0">Log Aktivitas (Audit Trail)</h5>
                <i class="bi bi-shield-check text-muted"></i>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="list-group list-group-flush overflow-auto" style="max-height: 300px;">
                    <?php $__empty_1 = true; $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item px-0 py-2 border-0">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark small"><?php echo e($log->user ? $log->user->name : 'System'); ?></span>
                                <small class="text-muted" style="font-size: 0.65rem;"><?php echo e($log->created_at->diffForHumans()); ?></small>
                            </div>
                            <p class="mb-0 text-muted small" style="line-height: 1.3;">
                                <span class="badge bg-secondary-subtle text-secondary py-0.5 px-1 me-1" style="font-size: 0.6rem; text-transform: uppercase;"><?php echo e($log->action); ?></span>
                                <?php echo e($log->description); ?>

                            </p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-5 text-muted">
                            <p class="mb-0 small">Belum ada catatan log aktivitas.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('monthlyChart');
        if (ctx) {
            const rawChartData = <?php echo json_encode($chartData, 15, 512) ?>;
            
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            const requestedData = rawChartData.map(d => d.requested);
            const paidData = rawChartData.map(d => d.paid);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Total Pengajuan (Rp)',
                            data: requestedData,
                            borderColor: '#0d9488', // Teal
                            backgroundColor: 'rgba(13, 148, 136, 0.05)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 3
                        },
                        {
                            label: 'Total Pembayaran (Rp)',
                            data: paidData,
                            borderColor: '#3b82f6', // Blue
                            backgroundColor: 'rgba(59, 130, 246, 0.05)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    family: "'Outfit', sans-serif",
                                    size: 11
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                },
                                font: {
                                    family: "'Outfit', sans-serif",
                                    size: 10
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.03)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: "'Outfit', sans-serif",
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\rifki\.gemini\antigravity\scratch\sistem-pengajuan-transaksi\resources\views/dashboard.blade.php ENDPATH**/ ?>