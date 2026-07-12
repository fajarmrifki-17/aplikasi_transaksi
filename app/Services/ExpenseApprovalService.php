<?php

namespace App\Services;

use App\Models\ExpenseRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExpenseApprovalService
{
    /**
     * Process approval action (Approve or Reject) for an expense request.
     */
    public function processApproval(ExpenseRequest $expenseRequest, string $action, ?string $notes, User $user): void
    {
        // 1. Validate if request is in a state that can be approved
        $allowed = false;
        $currentStatus = $expenseRequest->status;

        if ($currentStatus === 'Waiting Supervisor' && $user->isSupervisor()) {
            $allowed = true;
        } elseif ($currentStatus === 'Waiting Manager' && $user->isManager()) {
            $allowed = true;
        } elseif ($currentStatus === 'Waiting Director' && $user->isDirector()) {
            $allowed = true;
        } elseif ($currentStatus === 'Waiting Finance' && $user->isFinance()) {
            $allowed = true;
        }

        if (!$allowed) {
            throw new \Exception('Anda tidak memiliki wewenang untuk memberikan approval pada tahap ini.');
        }

        if ($action === 'Reject' && empty(trim($notes))) {
            throw new \Exception('Catatan penolakan (notes) wajib diisi jika pengajuan ditolak.');
        }

        // 2. Perform database transaction
        DB::transaction(function () use ($expenseRequest, $action, $notes, $user, $currentStatus) {
            // Determine next status
            $nextStatus = $expenseRequest->status;

            if ($action === 'Reject') {
                $nextStatus = 'Rejected';
            } else {
                // Action is Approve
                switch ($currentStatus) {
                    case 'Waiting Supervisor':
                        $nextStatus = 'Waiting Manager';
                        break;
                    case 'Waiting Manager':
                        $nextStatus = 'Waiting Director';
                        break;
                    case 'Waiting Director':
                        $nextStatus = 'Waiting Finance';
                        break;
                    case 'Waiting Finance':
                        // If approved by Finance, it transitions to Approved. It will be set to Paid after payment details are input.
                        $nextStatus = 'Approved';
                        break;
                }
            }

            // Update status
            $expenseRequest->update([
                'status' => $nextStatus,
            ]);

            // Save history
            $expenseRequest->approvalHistories()->create([
                'user_id' => $user->id,
                'role' => $user->role->display_name,
                'action' => $action,
                'notes' => $notes ?? ($action === 'Approve' ? 'Disetujui.' : ''),
            ]);

            // Log activity
            $logAction = $action === 'Approve' ? 'Approve' : 'Reject';
            ActivityLogger::log(
                $logAction,
                "User {$user->name} ({$user->role->display_name}) melakukan {$logAction} pada pengajuan {$expenseRequest->request_number} dengan status akhir: {$nextStatus}"
            );
        });
    }
}
