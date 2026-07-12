<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected BudgetService $budgetService;
    protected NotificationService $notificationService;

    public function __construct(BudgetService $budgetService, NotificationService $notificationService)
    {
        $this->budgetService = $budgetService;
        $this->notificationService = $notificationService;
    }

    /**
     * Process payment for an approved submission.
     */
    public function processPayment(Submission $submission, array $paymentData, User $financeUser): void
    {
        if ($submission->status !== 'Waiting Finance') {
            throw new \Exception('Pengajuan ini tidak berada dalam tahap menunggu pembayaran Finance.');
        }

        $amount = (float) $submission->requested_amount;
        $year = (int) date('Y', strtotime($submission->submission_date));
        $category = $submission->category;

        // Condition 5 & 8: Check if budget is sufficient
        if (!$this->budgetService->hasSufficientBudget($category, $amount, $year)) {
            // Transition status to Rejected if budget is insufficient
            DB::transaction(function () use ($submission, $financeUser) {
                $submission->update([
                    'status' => 'Rejected',
                ]);

                // Create approval history indicating the rejection reason
                $submission->approvals()->create([
                    'user_id' => $financeUser->id,
                    'role' => 'Finance',
                    'action' => 'Reject',
                    'notes' => 'Pembayaran ditolak karena anggaran kategori tidak mencukupi.',
                ]);

                ActivityLogger::log(
                    'Reject Payment',
                    "Pembayaran pengajuan {$submission->submission_number} ditolak oleh Finance karena anggaran kategori {$submission->category->name} tidak mencukupi."
                );

                $this->notificationService->sendSubmissionRejectedNotification(
                    $submission,
                    'Finance',
                    'Anggaran kategori pengeluaran tidak mencukupi (Over-budget).'
                );
            });

            throw new \Exception('Proses pembayaran dibatalkan dan pengajuan ditolak karena anggaran kategori tidak mencukupi.');
        }

        // Deduct budget and create payment entry in transaction
        DB::transaction(function () use ($submission, $paymentData, $financeUser, $category, $amount, $year) {
            // 1. Deduct budget
            $this->budgetService->deductBudget($category, $amount, $year);

            // 2. Create Payment record
            Payment::create([
                'submission_id' => $submission->id,
                'paid_by' => $financeUser->id,
                'amount' => $amount,
                'payment_date' => $paymentData['payment_date'] ?? now()->toDateString(),
                'reference_number' => $paymentData['reference_number'],
                'notes' => $paymentData['notes'] ?? null,
            ]);

            // 3. Update Submission Status to Paid
            $submission->update([
                'status' => 'Paid',
            ]);

            // 4. Record Approval log for payment completion
            $submission->approvals()->create([
                'user_id' => $financeUser->id,
                'role' => 'Finance',
                'action' => 'Approve',
                'notes' => 'Pembayaran selesai diproses.',
            ]);

            // 5. Log activity
            ActivityLogger::log(
                'Process Payment',
                "Memproses pembayaran pengajuan {$submission->submission_number} senilai Rp " . number_format($amount, 0, ',', '.') . " dengan Ref: {$paymentData['reference_number']}."
            );

            // 6. Send payment completed notification
            $this->notificationService->sendPaymentProcessedNotification($submission);
        });
    }
}
