<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;

class WorkflowService
{
    /**
     * Determine the next status for a submission after approval.
     */
    public function getNextStatusAfterApproval(Submission $submission): string
    {
        $currentStatus = $submission->status;
        $category = $submission->category;
        $amount = (float) $submission->requested_amount;

        // Condition 4: If Amount > 10,000,000 (Staff -> Supervisor -> Manager -> Director -> Finance)
        if ($amount > 10000000) {
            switch ($currentStatus) {
                case 'Submitted':
                case 'Draft':
                    return 'Waiting Supervisor Approval';
                case 'Waiting Supervisor Approval':
                    return 'Waiting Manager Approval';
                case 'Waiting Manager Approval':
                    return 'Waiting Director Approval';
                case 'Waiting Director Approval':
                    return 'Waiting Finance';
                default:
                    return $currentStatus;
            }
        }

        // Condition 1: If Category == "PO Produk" (Staff -> Director -> Finance)
        if ($category && $category->name === 'PO Produk') {
            switch ($currentStatus) {
                case 'Submitted':
                case 'Draft':
                    return 'Waiting Director Approval';
                case 'Waiting Director Approval':
                    return 'Waiting Finance';
                default:
                    return $currentStatus;
            }
        }

        // Condition 2: If Category != "PO Produk" AND Amount <= 5,000,000 (Staff -> Supervisor -> Finance)
        if ($amount <= 5000000) {
            switch ($currentStatus) {
                case 'Submitted':
                case 'Draft':
                    return 'Waiting Supervisor Approval';
                case 'Waiting Supervisor Approval':
                    return 'Waiting Finance';
                default:
                    return $currentStatus;
            }
        }

        // Condition 3: If Category != "PO Produk" AND Amount > 5,000,000 AND Amount <= 10,000,000 (Staff -> Supervisor -> Manager -> Finance)
        if ($amount > 5000000 && $amount <= 10000000) {
            switch ($currentStatus) {
                case 'Submitted':
                case 'Draft':
                    return 'Waiting Supervisor Approval';
                case 'Waiting Supervisor Approval':
                    return 'Waiting Manager Approval';
                case 'Waiting Manager Approval':
                    return 'Waiting Finance';
                default:
                    return $currentStatus;
            }
        }

        return $currentStatus;
    }

    /**
     * Check if a user is the correct next approver for the submission.
     */
    public function canUserApprove(User $user, Submission $submission): bool
    {
        $status = $submission->status;

        if ($status === 'Waiting Supervisor Approval' && $user->isSupervisor()) {
            return true;
        }

        if ($status === 'Waiting Manager Approval' && $user->isManager()) {
            return true;
        }

        if ($status === 'Waiting Director Approval' && $user->isDirector()) {
            return true;
        }

        if ($status === 'Waiting Finance' && $user->isFinance()) {
            return true;
        }

        return false;
    }
}
