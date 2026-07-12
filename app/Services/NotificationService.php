<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send email notification when a staff member creates a submission.
     */
    public function sendSubmissionCreatedNotification(Submission $submission): void
    {
        $applicant = $submission->user;
        
        $subject = "Pengajuan Baru: #{$submission->submission_number} - {$submission->category->name}";
        $content = "Halo {$applicant->name}, pengajuan transaksi Anda dengan nomor {$submission->submission_number} senilai Rp " . number_format($submission->requested_amount, 0, ',', '.') . " telah berhasil dibuat dalam status Draft.";

        $this->sendEmail($applicant->email, $subject, $content);
    }

    /**
     * Send email notification when approval is required from a role (Supervisor, Manager, Director).
     */
    public function sendApprovalRequiredNotification(Submission $submission, string $role): void
    {
        // Get all users in that role
        $approvers = User::role($role)->get();
        $subject = "Persetujuan Diperlukan: #{$submission->submission_number}";
        
        foreach ($approvers as $approver) {
            $content = "Halo {$approver->name}, pengajuan transaksi #{$submission->submission_number} senilai Rp " . number_format($submission->requested_amount, 0, ',', '.') . " dari {$submission->user->name} saat ini menunggu persetujuan Anda sebagai {$role}.";
            $this->sendEmail($approver->email, $subject, $content);
        }
    }

    /**
     * Send email notification when a submission is fully approved and waiting for Finance.
     */
    public function sendSubmissionApprovedNotification(Submission $submission): void
    {
        $applicant = $submission->user;
        $subject = "Pengajuan Disetujui: #{$submission->submission_number}";
        $content = "Halo {$applicant->name}, pengajuan transaksi Anda #{$submission->submission_number} telah disetujui oleh seluruh Approver dan sekarang sedang divalidasi oleh Finance.";

        $this->sendEmail($applicant->email, $subject, $content);

        // Notify Finance
        $financeUsers = User::role('Finance')->get();
        foreach ($financeUsers as $finance) {
            $financeContent = "Halo {$finance->name}, pengajuan #{$submission->submission_number} senilai Rp " . number_format($submission->requested_amount, 0, ',', '.') . " telah disetujui oleh para Approver dan siap diproses pembayarannya.";
            $this->sendEmail($finance->email, "Pembayaran Siap Diproses: #{$submission->submission_number}", $financeContent);
        }
    }

    /**
     * Send email notification when a submission is rejected.
     */
    public function sendSubmissionRejectedNotification(Submission $submission, string $rejectorRole, string $notes): void
    {
        $applicant = $submission->user;
        $subject = "Pengajuan Ditolak: #{$submission->submission_number}";
        $content = "Halo {$applicant->name}, pengajuan transaksi Anda #{$submission->submission_number} telah ditolak oleh {$rejectorRole}.\n\nCatatan Penolakan: \"{$notes}\"";

        $this->sendEmail($applicant->email, $subject, $content);
    }

    /**
     * Send email notification when a payment is processed.
     */
    public function sendPaymentProcessedNotification(Submission $submission): void
    {
        $applicant = $submission->user;
        $payment = $submission->payment;
        
        $subject = "Pembayaran Selesai: #{$submission->submission_number}";
        $content = "Halo {$applicant->name}, pengajuan transaksi Anda #{$submission->submission_number} telah berhasil dibayarkan senilai Rp " . number_format($submission->requested_amount, 0, ',', '.') . " pada tanggal {$payment->payment_date->format('d-m-Y')} dengan nomor referensi bank: {$payment->reference_number}.";

        $this->sendEmail($applicant->email, $subject, $content);
    }

    /**
     * Helper method to send email. In development, it logs the email to local storage.
     */
    private function sendEmail(string $to, string $subject, string $content): void
    {
        Log::info("EMAIL NOTIFICATION TO: {$to} | SUBJECT: {$subject} | CONTENT: {$content}");

        try {
            // If mail is configured, send actual mail (silent failure if not configured to prevent crashes in recruitment sandbox)
            Mail::raw($content, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
        } catch (\Exception $e) {
            Log::warning("Gagal mengirim email riil ke {$to}: " . $e->getMessage());
        }
    }
}
