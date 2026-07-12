<?php $__env->startSection('title', 'Buat Pengajuan Transaksi'); ?>
<?php $__env->startSection('page-title', 'Buat Pengajuan Baru'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('submissions.index')); ?>">Pengajuan Saya</a></li>
    <li class="breadcrumb-item active" aria-current="page">Buat Baru</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-plus text-primary me-2"></i>Form Pengajuan Transaksi</h5>
            </div>
            
            <div class="card-body p-4">
                <form id="submissionForm" method="POST" action="<?php echo e(route('submissions.store')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <!-- Category Field -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold text-muted small">Kategori Pengeluaran <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select bg-light <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id') == $category->id ? 'selected' : ''); ?>>
                                    <?php echo e($category->name); ?> (<?php echo e($category->code); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['category_id'];
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

                    <!-- Requested Amount Field -->
                    <div class="mb-3">
                        <label for="requested_amount_display" class="form-label fw-semibold text-muted small">Nominal Pengajuan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-secondary-subtle border-0 text-dark fw-bold">Rp</span>
                            <input type="text" id="requested_amount_display" class="form-control bg-light border-0 fw-bold fs-5 text-dark" placeholder="0" required>
                            <input type="hidden" name="requested_amount" id="requested_amount" value="<?php echo e(old('requested_amount')); ?>">
                        </div>
                        <small class="text-muted" id="amount_formatted_word"></small>
                        <?php $__errorArgs = ['requested_amount'];
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

                    <!-- Description Field -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold text-muted small">Deskripsi Keperluan <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control bg-light <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="5" placeholder="Tuliskan detail keperluan pengeluaran..." required><?php echo e(old('description')); ?></textarea>
                        <?php $__errorArgs = ['description'];
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

                    <!-- Attachments Upload Field -->
                    <div class="mb-4">
                        <label for="attachments" class="form-label fw-semibold text-muted small">Berkas Pendukung (Multiple Upload) <span class="text-muted">(Opsional)</span></label>
                        <input type="file" name="attachments[]" id="attachments" class="form-control bg-light <?php $__errorArgs = ['attachments'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" multiple>
                        <div class="form-text text-muted small mt-1">
                            <i class="bi bi-info-circle me-1"></i>Format yang didukung: <strong>PDF, JPG, JPEG, PNG</strong>. Maksimal <strong>5 MB</strong> per berkas.
                        </div>
                        <div id="fileList" class="mt-2 d-flex flex-column gap-1"></div>
                        <?php $__errorArgs = ['attachments'];
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

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="<?php echo e(route('submissions.index')); ?>" class="btn btn-outline-premium">
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\rifki\.gemini\antigravity\scratch\sistem-pengajuan-transaksi\resources\views/submissions/create.blade.php ENDPATH**/ ?>