<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalRequest;
use App\Models\Submission;
use App\Repositories\CategoryRepository;
use App\Repositories\SubmissionRepository;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ApprovalController extends Controller implements HasMiddleware
{
    protected SubmissionRepository $submissionRepository;
    protected CategoryRepository $categoryRepository;
    protected ApprovalService $approvalService;

    public static function middleware(): array
    {
        return [
            'role:Supervisor|Manager|Director',
        ];
    }

    public function __construct(
        SubmissionRepository $submissionRepository,
        CategoryRepository $categoryRepository,
        ApprovalService $approvalService
    ) {
        $this->submissionRepository = $submissionRepository;
        $this->categoryRepository = $categoryRepository;
        $this->approvalService = $approvalService;
    }

    /**
     * Display a listing of submissions waiting for approval based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = '';
        if ($user->isSupervisor()) $role = 'Supervisor';
        elseif ($user->isManager()) $role = 'Manager';
        elseif ($user->isDirector()) $role = 'Director';

        $filters = $request->only(['search', 'category_id', 'start_date', 'end_date']);
        $submissions = $this->submissionRepository->getPaginatedForApprover($role, 10, $filters);
        $categories = $this->categoryRepository->all();

        return view('approvals.index', compact('submissions', 'categories'));
    }

    /**
     * Show the approval interface for a specific submission.
     */
    public function show(Submission $submission)
    {
        $submission = $this->submissionRepository->find($submission->id);
        
        // Safety check: ensure submission status matches approver role
        $user = Auth::user();
        $isAuthorized = false;

        if ($submission->status === 'Waiting Supervisor Approval' && $user->isSupervisor()) {
            $isAuthorized = true;
        } elseif ($submission->status === 'Waiting Manager Approval' && $user->isManager()) {
            $isAuthorized = true;
        } elseif ($submission->status === 'Waiting Director Approval' && $user->isDirector()) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            return redirect()
                ->route('approvals.index')
                ->with('warning', 'Pengajuan ini tidak berada dalam tahap approval Anda.');
        }

        return view('approvals.show', compact('submission'));
    }

    /**
     * Submit an approval action (Approve or Reject).
     */
    public function action(ApprovalRequest $request, Submission $submission)
    {
        try {
            $this->approvalService->processApproval(
                $submission,
                $request->action,
                $request->notes,
                Auth::user()
            );

            $message = $request->action === 'Approve' 
                ? "Pengajuan {$submission->submission_number} berhasil disetujui." 
                : "Pengajuan {$submission->submission_number} telah ditolak.";

            return redirect()
                ->route('approvals.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses approval: ' . $e->getMessage());
        }
    }
}
