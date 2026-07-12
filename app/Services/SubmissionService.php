<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class SubmissionService
{
    protected WorkflowService $workflowService;
    protected NotificationService $notificationService;

    public function __construct(WorkflowService $workflowService, NotificationService $notificationService)
    {
        $this->workflowService = $workflowService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new transaction submission.
     */
    public function createSubmission(array $data, array $files, User $user): Submission
    {
        return DB::transaction(function () use ($data, $files, $user) {
            // Generate unique submission number
            $today = date('Ymd');
            $prefix = "SUB/{$today}/";
            
            $countToday = Submission::whereDate('created_at', today())->count();
            $sequence = str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
            $submissionNumber = $prefix . $sequence;

            while (Submission::where('submission_number', $submissionNumber)->exists()) {
                $countToday++;
                $sequence = str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
                $submissionNumber = $prefix . $sequence;
            }

            // Create submission (Status starts as Draft)
            $submission = Submission::create([
                'submission_number' => $submissionNumber,
                'submission_date' => now()->toDateString(),
                'user_id' => $user->id,
                'category_id' => $data['category_id'],
                'requested_amount' => $data['requested_amount'],
                'description' => $data['description'],
                'status' => 'Draft',
            ]);

            // Save supporting files
            $this->uploadFiles($submission, $files);

            // Log activity
            ActivityLogger::log(
                'Create Submission',
                "Membuat pengajuan transaksi baru {$submission->submission_number} senilai Rp " . number_format($submission->requested_amount, 0, ',', '.')
            );

            // Notify user
            $this->notificationService->sendSubmissionCreatedNotification($submission);

            return $submission;
        });
    }

    /**
     * Update an existing submission (Draft only).
     */
    public function updateSubmission(Submission $submission, array $data, array $files): Submission
    {
        if ($submission->status !== 'Draft') {
            throw new \Exception('Hanya pengajuan dengan status Draft yang dapat diubah.');
        }

        return DB::transaction(function () use ($submission, $data, $files) {
            $submission->update([
                'category_id' => $data['category_id'],
                'requested_amount' => $data['requested_amount'],
                'description' => $data['description'],
            ]);

            // If new files are uploaded, we append them or overwrite them (we will append them)
            if (!empty($files)) {
                $this->uploadFiles($submission, $files);
            }

            // Log activity
            ActivityLogger::log(
                'Edit Submission',
                "Mengubah pengajuan transaksi {$submission->submission_number}"
            );

            return $submission;
        });
    }

    /**
     * Delete a submission (Draft only).
     */
    public function deleteSubmission(Submission $submission): void
    {
        if ($submission->status !== 'Draft') {
            throw new \Exception('Hanya pengajuan dengan status Draft yang dapat dihapus.');
        }

        DB::transaction(function () use ($submission) {
            // Delete physical files
            foreach ($submission->submissionFiles as $file) {
                Storage::disk('private')->delete($file->file_path);
                $file->delete();
            }

            $submission->delete();

            // Log activity
            ActivityLogger::log(
                'Delete Submission',
                "Menghapus pengajuan transaksi {$submission->submission_number}"
            );
        });
    }

    /**
     * Submit submission to begin approval workflow.
     */
    public function submitSubmission(Submission $submission, User $user): void
    {
        if ($submission->status !== 'Draft') {
            throw new \Exception('Hanya pengajuan dengan status Draft yang dapat diajukan ke workflow.');
        }

        DB::transaction(function () use ($submission, $user) {
            // Calculate next status based on category and amount thresholds
            $nextStatus = $this->workflowService->getNextStatusAfterApproval($submission);

            $submission->update([
                'status' => $nextStatus,
            ]);

            // Add Approval history entry
            $submission->approvals()->create([
                'user_id' => $user->id,
                'role' => 'Staff',
                'action' => 'Submit',
                'notes' => 'Pengajuan diajukan ke proses approval.',
            ]);

            // Log activity
            ActivityLogger::log(
                'Submit Submission',
                "Mengirim pengajuan {$submission->submission_number} ke workflow. Status: {$nextStatus}."
            );

            // Send notification to the next role
            $nextRole = $this->getNextApproverRole($nextStatus);
            if ($nextRole) {
                $this->notificationService->sendApprovalRequiredNotification($submission, $nextRole);
            }
        });
    }

    /**
     * Helper to save uploaded files securely.
     */
    private function uploadFiles(Submission $submission, array $files): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                // Secure store: files are stored in 'private/submission_files' (not public)
                // to prevent direct web URL access. They will be served via a controller route checking permissions.
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileType = $file->getClientMimeType();

                // Prevent executable files
                $extension = strtolower($file->getClientOriginalExtension());
                $forbidden = ['php', 'phtml', 'sh', 'bat', 'exe', 'cgi', 'pl', 'py', 'js', 'jar'];
                if (in_array($extension, $forbidden)) {
                    throw new \Exception("File tipe .{$extension} tidak diijinkan untuk alasan keamanan.");
                }

                $filePath = $file->store('submission_files', 'private');

                SubmissionFile::create([
                    'submission_id' => $submission->id,
                    'file_path' => $filePath,
                    'file_name' => $originalName,
                    'file_size' => $fileSize,
                    'file_type' => $fileType,
                ]);
            }
        }
    }

    /**
     * Map status to the approver role name.
     */
    private function getNextApproverRole(string $status): ?string
    {
        switch ($status) {
            case 'Waiting Supervisor Approval':
                return 'Supervisor';
            case 'Waiting Manager Approval':
                return 'Manager';
            case 'Waiting Director Approval':
                return 'Director';
            case 'Waiting Finance':
                return 'Finance';
            default:
                return null;
        }
    }
}
