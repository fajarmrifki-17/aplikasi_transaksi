<?php $__env->startSection('title', 'Proses Persetujuan Transaksi'); ?>
<?php $__env->startSection('page-title', 'Proses Persetujuan'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('approvals.index')); ?>">Antrian Approval</a></li>
    <li class="breadcrumb-item active" aria-current="page">#<?php echo e($submission->submission_number); ?></li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4">
    <!-- Left Column: Details -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>Detail Pengajuan</h5>
                <span class="badge-status badge-waiting-supervisor fs-6"><?php echo e($submission->status); ?></span>
            </div>
            
            <div class="card-body p-4 border-top">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Nomor Pengajuan</label>
                        <span class="fw-bold text-dark fs-5"><?php echo e($submission->submission_number); ?></span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Tanggal Pengajuan</label>
                        <span class="fw-medium text-dark"><?php echo e($submission->submission_date->format('d F Y')); ?></span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Pemohon</label>
                        <span class="fw-medium text-dark"><?php echo e($submission->user->name); ?> (<?php echo e($submission->user->email); ?>)</span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Kategori Pengeluaran</label>
                        <span class="fw-medium text-dark"><?php echo e($submission->category->name); ?></span>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Nominal Transaksi</label>
                        <span class="fw-bold text-primary fs-3">Rp <?php echo e(number_format($submission->requested_amount, 2, ',', '.')); ?></span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small fw-semibold text-uppercase d-block mb-2">Deskripsi Keperluan</label>
                    <div class="p-3 bg-light rounded text-dark text-break" style="white-space: pre-line; line-height: 1.5; font-size: 0.95rem;">
                        <?php echo e($submission->description); ?>

                    </div>
                </div>

                <!-- Attachments -->
                <div class="mb-4">
                    <label class="text-muted small fw-semibold text-uppercase d-block mb-2">Berkas Lampiran Pendukung</label>
                    <?php if($submission->submissionFiles->isEmpty()): ?>
                        <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Tidak ada lampiran berkas pendukung.</p>
                    <?php else: ?>
                        <div class="row g-2">
                            <?php $__currentLoopData = $submission->submissionFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-12 col-sm-6">
                                    <div class="border d-flex align-items-center justify-content-between p-3 rounded bg-light">
                                        <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                            <i class="bi bi-file-earmark-check text-success fs-4 flex-shrink-0"></i>
                                            <div class="text-truncate small">
                                                <div class="fw-semibold text-dark text-truncate" title="<?php echo e($file->file_name); ?>"><?php echo e($file->file_name); ?></div>
                                                <div class="text-muted"><?php echo e(number_format($file->file_size / 1024, 1)); ?> KB</div>
                                            </div>
                                        </div>
                                        <a href="<?php echo e(route('submissions.download', $file)); ?>" class="btn btn-sm btn-outline-primary-premium flex-shrink-0 px-2 py-1" title="Unduh Berkas">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Approval Decision Action Card -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shield-check text-success me-2"></i>Form Keputusan Approval</h5>
            </div>
            
            <div class="card-body p-4 border-top">
                <form id="approvalForm" method="POST" action="<?php echo e(route('approvals.action', $submission)); ?>">
                    <?php echo csrf_field(); ?>

                    <!-- Decision Options -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small d-block">Keputusan Tindakan <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="action" id="actionApprove" value="Approve" autocomplete="off" checked>
                            <label class="btn btn-outline-success py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" for="actionApprove">
                                <i class="bi bi-check-circle"></i> SETUJUI (APPROVE)
                            </label>
                            
                            <input type="radio" class="btn-check" name="action" id="actionReject" value="Reject" autocomplete="off">
                            <label class="btn btn-outline-danger py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" for="actionReject">
                                <i class="bi bi-x-circle"></i> TOLAK (REJECT)
                            </label>
                        </div>
                        <?php $__errorArgs = ['action'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Approval Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold text-muted small">Catatan Persetujuan / Penolakan <span id="notesRequiredStar" class="text-danger d-none">*</span></label>
                        <textarea name="notes" id="notes" class="form-control bg-light <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="4" placeholder="Tulis catatan persetujuan Anda..." required></textarea>
                        <div class="form-text text-muted small mt-1" id="notesHelpText">
                            Catatan bersifat opsional untuk persetujuan (Approve) namun <strong>wajib diisi</strong> jika Anda menolak (Reject) pengajuan.
                        </div>
                        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?php echo e(route('approvals.index')); ?>" class="btn btn-outline-premium">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary-premium" id="btnSubmitDecision">
                            <i class="bi bi-check-lg"></i> Proses Keputusan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Timeline -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Timeline Persetujuan</h5>
            </div>
            
            <div class="card-body p-4 border-top">
                <div class="position-relative">
                    <div class="position-absolute start-0 h-100 border-start border-2 border-secondary-subtle" style="left: 15px !important; z-index: 1;"></div>
                    
                    <?php $__currentLoopData = $submission->approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $timelineIcon = 'bi-circle';
                            $timelineBg = 'bg-secondary';
                            if ($approval->action === 'Submit') {
                                $timelineIcon = 'bi-send-fill';
                                $timelineBg = 'bg-primary text-white';
                            } elseif ($approval->action === 'Approve') {
                                $timelineIcon = 'bi-check-lg';
                                $timelineBg = 'bg-success text-white';
                            } elseif ($approval->action === 'Reject') {
                                $timelineIcon = 'bi-x-lg';
                                $timelineBg = 'bg-danger text-white';
                            }
                        ?>
                        <div class="d-flex mb-4 position-relative" style="z-index: 2;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.85rem; <?php echo e($timelineBg === 'bg-success text-white' ? 'background-color: #10b981;' : ($timelineBg === 'bg-danger text-white' ? 'background-color: #ef4444;' : ($timelineBg === 'bg-primary text-white' ? 'background-color: #3b82f6;' : 'background-color: #94a3b8;'))); ?> color: white;">
                                <i class="bi <?php echo e($timelineIcon); ?>"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold text-dark mb-0 small"><?php echo e($approval->user->name); ?></h6>
                                    <small class="text-muted" style="font-size: 0.7rem;"><?php echo e($approval->created_at->format('d-m-Y H:i')); ?></small>
                                </div>
                                <div class="small mb-1">
                                    <span class="badge bg-secondary-subtle text-secondary py-0.5 px-1 me-1" style="font-size: 0.6rem; text-transform: uppercase;"><?php echo e($approval->role); ?></span>
                                    <span class="fw-semibold <?php echo e($approval->action === 'Approve' ? 'text-success' : ($approval->action === 'Reject' ? 'text-danger' : 'text-primary')); ?>" style="font-size: 0.8rem;">
                                        <?php echo e($approval->action === 'Submit' ? 'Mengajukan' : ($approval->action === 'Approve' ? 'Menyetujui' : 'Menolak')); ?>

                                    </span>
                                </div>
                                <?php if($approval->notes): ?>
                                    <div class="p-2 bg-light rounded text-muted text-break" style="font-size: 0.78rem; font-style: italic;">
                                        "<?php echo e($approval->notes); ?>"
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const actionApprove = document.getElementById('actionApprove');
        const actionReject = document.getElementById('actionReject');
        const notesTextarea = document.getElementById('notes');
        const star = document.getElementById('notesRequiredStar');
        const form = document.getElementById('approvalForm');

        function toggleNotesRequirement() {
            if (actionReject.checked) {
                notesTextarea.required = true;
                notesTextarea.placeholder = "Tuliskan alasan penolakan pengajuan (Wajib diisi)...";
                star.classList.remove('d-none');
            } else {
                notesTextarea.required = false;
                notesTextarea.placeholder = "Tulis catatan persetujuan Anda (Opsional)...";
                star.classList.add('d-none');
            }
        }

        actionApprove.addEventListener('change', toggleNotesRequirement);
        actionReject.addEventListener('change', toggleNotesRequirement);

        // Initial setup
        toggleNotesRequirement();

        // SweetAlert Confirmation on Submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const actionText = actionApprove.checked ? 'Menyetujui' : 'Menolak';
            const actionIcon = actionApprove.checked ? 'success' : 'warning';
            const actionColor = actionApprove.checked ? '#10b981' : '#ef4444';

            if (actionReject.checked && !notesTextarea.value.trim()) {
                Swal.fire({
                    title: 'Kesalahan Validasi!',
                    text: 'Catatan wajib diisi apabila Anda menolak pengajuan.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: `Anda akan memproses keputusan ${actionText.toUpperCase()} untuk pengajuan ini.`,
                icon: actionIcon,
                showCancelButton: true,
                confirmButtonColor: actionColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\rifki\.gemini\antigravity\scratch\sistem-pengajuan-transaksi\resources\views/approvals/show.blade.php ENDPATH**/ ?>