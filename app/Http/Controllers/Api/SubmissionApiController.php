<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Repositories\SubmissionRepository;
use App\Services\SubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubmissionApiController extends Controller
{
    protected SubmissionRepository $submissionRepository;
    protected SubmissionService $submissionService;

    public function __construct(SubmissionRepository $submissionRepository, SubmissionService $submissionService)
    {
        $this->submissionRepository = $submissionRepository;
        $this->submissionService = $submissionService;
    }

    /**
     * Get list of submissions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->only(['search', 'category_id', 'status', 'start_date', 'end_date']);

        if ($user->isStaff()) {
            $submissions = $this->submissionRepository->getPaginatedForUser($user, 15, $filters);
        } elseif ($user->isFinance() || $user->isDirector() || $user->isManager()) {
            $submissions = $this->submissionRepository->getPaginatedForFinance(15, $filters);
        } else {
            $role = $user->isSupervisor() ? 'Supervisor' : '';
            $submissions = $this->submissionRepository->getPaginatedForApprover($role, 15, $filters);
        }

        return response()->json([
            'status' => 'success',
            'data' => $submissions
        ]);
    }

    /**
     * Store new submission.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('submission.create')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => ['required', 'exists:categories,id'],
            'requested_amount' => ['required', 'numeric', 'min:1000'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            // Note: API creations does not handle file uploads in this basic endpoint (or accepts them via multipart/form-data)
            $files = $request->file('attachments') ?? [];
            $submission = $this->submissionService->createSubmission($validator->validated(), $files, $user);

            return response()->json([
                'status' => 'success',
                'message' => 'Submission created successfully.',
                'data' => $submission
            ], 210);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Show details of a submission.
     */
    public function show(int $id)
    {
        $submission = $this->submissionRepository->find($id);

        if (!$submission) {
            return response()->json(['status' => 'error', 'message' => 'Submission not found.'], 404);
        }

        $user = Auth::user();
        if ($user->isStaff() && $submission->user_id !== $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Access denied.'], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $submission
        ]);
    }

    /**
     * Update an existing submission (Draft only).
     */
    public function update(Request $request, int $id)
    {
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json(['status' => 'error', 'message' => 'Submission not found.'], 404);
        }

        $user = Auth::user();
        if ($submission->user_id !== $user->id || !$user->can('submission.update')) {
            return response()->json(['status' => 'error', 'message' => 'Access denied.'], 403);
        }

        if ($submission->status !== 'Draft') {
            return response()->json(['status' => 'error', 'message' => 'Only Draft submissions can be updated.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => ['required', 'exists:categories,id'],
            'requested_amount' => ['required', 'numeric', 'min:1000'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $files = $request->file('attachments') ?? [];
            $updated = $this->submissionService->updateSubmission($submission, $validator->validated(), $files);

            return response()->json([
                'status' => 'success',
                'message' => 'Submission updated successfully.',
                'data' => $updated
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Delete a submission (Draft only).
     */
    public function destroy(int $id)
    {
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json(['status' => 'error', 'message' => 'Submission not found.'], 404);
        }

        $user = Auth::user();
        if ($submission->user_id !== $user->id || !$user->can('submission.delete')) {
            return response()->json(['status' => 'error', 'message' => 'Access denied.'], 403);
        }

        try {
            $this->submissionService->deleteSubmission($submission);
            return response()->json([
                'status' => 'success',
                'message' => 'Submission deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Submit a submission to workflow.
     */
    public function submit(int $id)
    {
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json(['status' => 'error', 'message' => 'Submission not found.'], 404);
        }

        $user = Auth::user();
        if ($submission->user_id !== $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Access denied.'], 403);
        }

        try {
            $this->submissionService->submitSubmission($submission, $user);
            return response()->json([
                'status' => 'success',
                'message' => 'Submission successfully submitted to approval workflow.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
