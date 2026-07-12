<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\ExpenseRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseRequestService
{
    /**
     * Create a new expense request.
     */
    public function createRequest(array $data, $attachmentFile, User $user): ExpenseRequest
    {
        return DB::transaction(function () use ($data, $attachmentFile, $user) {
            // Generate request number
            $today = date('Ymd');
            $prefix = "EXP/{$today}/";
            
            // Get count of requests created today
            $countToday = ExpenseRequest::whereDate('created_at', today())->count();
            $sequence = str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
            $requestNumber = $prefix . $sequence;

            // Ensure uniqueness
            while (ExpenseRequest::where('request_number', $requestNumber)->exists()) {
                $countToday++;
                $sequence = str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
                $requestNumber = $prefix . $sequence;
            }

            // Create expense request (Status defaults to Draft)
            $expenseRequest = ExpenseRequest::create([
                'request_number' => $requestNumber,
                'request_date' => now()->toDateString(),
                'user_id' => $user->id,
                'department_id' => $user->department_id ?? $data['department_id'] ?? 1, // Fallback to IT if null
                'expense_category_id' => $data['expense_category_id'],
                'amount' => $data['amount'],
                'vendor' => $data['vendor'],
                'description' => $data['description'],
                'urgency' => $data['urgency'] ?? 'Medium',
                'needed_date' => $data['needed_date'],
                'status' => 'Draft',
            ]);

            // Handle file upload
            if ($attachmentFile) {
                $originalName = $attachmentFile->getClientOriginalName();
                $fileSize = $attachmentFile->getSize();
                $fileType = $attachmentFile->getClientMimeType();
                
                // Store in public disk under 'attachments'
                $filePath = $attachmentFile->store('attachments', 'public');

                Attachment::create([
                    'expense_request_id' => $expenseRequest->id,
                    'file_path' => $filePath,
                    'file_name' => $originalName,
                    'file_size' => $fileSize,
                    'file_type' => $fileType,
                ]);
            }

            ActivityLogger::log(
                'Create Request',
                "Membuat pengajuan transaksi baru {$expenseRequest->request_number} senilai Rp " . number_format($expenseRequest->amount, 0, ',', '.')
            );

            return $expenseRequest;
        });
    }

    /**
     * Update an existing expense request (Draft only).
     */
    public function updateRequest(ExpenseRequest $expenseRequest, array $data, $attachmentFile): ExpenseRequest
    {
        if ($expenseRequest->status !== 'Draft') {
            throw new \Exception('Hanya pengajuan dengan status Draft yang dapat diubah.');
        }

        return DB::transaction(function () use ($expenseRequest, $data, $attachmentFile) {
            $expenseRequest->update([
                'expense_category_id' => $data['expense_category_id'],
                'amount' => $data['amount'],
                'vendor' => $data['vendor'],
                'description' => $data['description'],
                'urgency' => $data['urgency'],
                'needed_date' => $data['needed_date'],
            ]);

            if ($attachmentFile) {
                // Delete old attachments physically and from database
                foreach ($expenseRequest->attachments as $oldAttachment) {
                    Storage::disk('public')->delete($oldAttachment->file_path);
                    $oldAttachment->delete();
                }

                $originalName = $attachmentFile->getClientOriginalName();
                $fileSize = $attachmentFile->getSize();
                $fileType = $attachmentFile->getClientMimeType();
                $filePath = $attachmentFile->store('attachments', 'public');

                Attachment::create([
                    'expense_request_id' => $expenseRequest->id,
                    'file_path' => $filePath,
                    'file_name' => $originalName,
                    'file_size' => $fileSize,
                    'file_type' => $fileType,
                ]);
            }

            ActivityLogger::log(
                'Edit Request',
                "Mengubah pengajuan transaksi {$expenseRequest->request_number}"
            );

            return $expenseRequest;
        });
    }

    /**
     * Submit expense request to workflow.
     */
    public function submitRequest(ExpenseRequest $expenseRequest, User $user): void
    {
        if ($expenseRequest->status !== 'Draft') {
            throw new \Exception('Hanya pengajuan dengan status Draft yang dapat dikirim.');
        }

        DB::transaction(function () use ($expenseRequest, $user) {
            $expenseRequest->update([
                'status' => 'Waiting Supervisor',
            ]);

            // Log approval history for submission
            $expenseRequest->approvalHistories()->create([
                'user_id' => $user->id,
                'role' => $user->role->name,
                'action' => 'Submit',
                'notes' => 'Pengajuan diserahkan untuk proses approval.',
            ]);

            ActivityLogger::log(
                'Submit Request',
                "Mengirim pengajuan transaksi {$expenseRequest->request_number} ke Supervisor"
            );
        });
    }
}
