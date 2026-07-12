<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    protected WorkflowService $workflowService;
    protected NotificationService $notificationService;

    public function __construct(WorkflowService $workflowService, NotificationService $notificationService)
    {
        $this->workflowService = $workflowService;
        $this->notificationService = $notificationService;
    }

    /**
     * Process approval or rejection action for a submission.
     */
    public function processApproval(Submission $submission, string $action, ?string $notes, User $user): void
    {
        // 1. Validate if user can approve
        if (!$this->workflowService->canUserApprove($user, $submission)) {
            throw new \Exception('Anda tidak memiliki wewenang untuk memproses approval pada tahap ini.');
        }

        if ($action === 'Reject' && empty(trim($notes))) {
            throw new \Exception('Catatan penolakan (notes) wajib diisi.');
        }

        $currentRole = $this->getUserRoleName($user);

        // 2. Process database transition
        DB::transaction(function () use ($submission, $action, $notes, $user, $currentRole) {
            $nextStatus = $submission->status;

            if ($action === 'Reject') {
                $nextStatus = 'Rejected';
            } else {
                // Determine next status in workflow
                $nextStatus = $this->workflowService->getNextStatusAfterApproval($submission);
            }

            // Update submission status
            $submission->update([
                'status' => $nextStatus,
            ]);

            // Save approval log
            $submission->approvals()->create([
                'user_id' => $user->id,
                'role' => $currentRole,
                'action' => $action,
                'notes' => $notes ?? ($action === 'Approve' ? 'Disetujui.' : ''),
            ]);

            // Log activity log
            ActivityLogger::log(
                $action === 'Approve' ? 'Approve Submission' : 'Reject Submission',
                "User {$user->name} ({$currentRole}) melakukan {$action} pada pengajuan {$submission->submission_number}. Status akhir: {$nextStatus}."
            );

            // Send notifications
            if ($action === 'Reject') {
                $this->notificationService->sendSubmissionRejectedNotification($submission, $currentRole, $notes);
            } else {
                // If it transitioned to Waiting Finance (all approvals done)
                if ($nextStatus === 'Waiting Finance') {
                    $this->notificationService->sendSubmissionApprovedNotification($submission);
                } else {
                    // Send to next approver role
                    $nextRole = $this->getNextApproverRole($nextStatus);
                    if ($nextRole) {
                        $this->notificationService->sendApprovalRequiredNotification($submission, $nextRole);
                    }
                }
            }
        });
    }

    /**
     * Map user role object to display name.
     */
    private function getUserRoleName(User $user): string
    {
        if ($user->isSupervisor()) return 'Supervisor';
        if ($user->isManager()) return 'Manager';
        if ($user->isDirector()) return 'Director';
        if ($user->isFinance()) return 'Finance';
        return 'Staff';
    }

    /**
     * Map status to the next role.
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
