<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Requests\UpdateSubmissionRequest;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Repositories\CategoryRepository;
use App\Repositories\SubmissionRepository;
use App\Services\SubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SubmissionController extends Controller implements HasMiddleware
{
    protected SubmissionRepository $submissionRepository;
    protected CategoryRepository $categoryRepository;
    protected SubmissionService $submissionService;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:submission.create', only: ['create', 'store']),
            new Middleware('permission:submission.read', only: ['index', 'show', 'downloadFile']),
            new Middleware('permission:submission.update', only: ['edit', 'update']),
            new Middleware('permission:submission.delete', only: ['destroy']),
        ];
    }

    public function __construct(
        SubmissionRepository $submissionRepository,
        CategoryRepository $categoryRepository,
        SubmissionService $submissionService
    ) {
        $this->submissionRepository = $submissionRepository;
        $this->categoryRepository = $categoryRepository;
        $this->submissionService = $submissionService;
    }

    /**
     * Display a listing of user's submissions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->only(['search', 'category_id', 'status', 'start_date', 'end_date']);
        
        $submissions = $this->submissionRepository->getPaginatedForUser($user, 10, $filters);
        $categories = $this->categoryRepository->all();

        return view('submissions.index', compact('submissions', 'categories'));
    }

    /**
     * Show the form for creating a new submission.
     */
    public function create()
    {
        $categories = $this->categoryRepository->all();
        return view('submissions.create', compact('categories'));
    }

    /**
     * Store a newly created submission.
     */
    public function store(StoreSubmissionRequest $request)
    {
        try {
            $files = $request->file('attachments') ?? [];
            $submission = $this->submissionService->createSubmission(
                $request->validated(),
                $files,
                Auth::user()
            );

            return redirect()
                ->route('submissions.index')
                ->with('success', "Pengajuan transaksi {$submission->submission_number} berhasil dibuat dalam status Draft.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified submission.
     */
    public function show(Submission $submission)
    {
        $user = Auth::user();
        
        // Authorization: Staff can only view their own submissions, others can view if they have read access
        if ($user->isStaff() && $submission->user_id !== $user->id) {
            abort(403, 'Anda tidak diijinkan melihat pengajuan milik pengguna lain.');
        }

        // Refetch with relations
        $submission = $this->submissionRepository->find($submission->id);

        return view('submissions.show', compact('submission'));
    }

    /**
     * Show the form for editing the specified submission (Draft only).
     */
    public function edit(Submission $submission)
    {
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diijinkan mengedit pengajuan ini.');
        }

        if ($submission->status !== 'Draft') {
            return redirect()
                ->route('submissions.show', $submission)
                ->with('warning', 'Hanya pengajuan dengan status Draft yang dapat diubah.');
        }

        $categories = $this->categoryRepository->all();
        return view('submissions.edit', compact('submission', 'categories'));
    }

    /**
     * Update the specified submission (Draft only).
     */
    public function update(UpdateSubmissionRequest $request, Submission $submission)
    {
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diijinkan mengedit pengajuan ini.');
        }

        try {
            $files = $request->file('attachments') ?? [];
            $this->submissionService->updateSubmission($submission, $request->validated(), $files);

            return redirect()
                ->route('submissions.show', $submission)
                ->with('success', 'Pengajuan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified submission (Draft only).
     */
    public function destroy(Submission $submission)
    {
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diijinkan menghapus pengajuan ini.');
        }

        try {
            $this->submissionService->deleteSubmission($submission);
            return redirect()
                ->route('submissions.index')
                ->with('success', 'Pengajuan transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Submit submission to workflow (Draft only).
     */
    public function submit(Submission $submission)
    {
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diijinkan memproses pengajuan ini.');
        }

        try {
            $this->submissionService->submitSubmission($submission, Auth::user());
            return redirect()
                ->route('submissions.index')
                ->with('success', "Pengajuan transaksi {$submission->submission_number} berhasil dikirim ke workflow persetujuan.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Download files securely from private storage.
     */
    public function downloadFile(SubmissionFile $file)
    {
        $submission = $file->submission;
        $user = Auth::user();

        // Check if user is allowed to read this submission
        if ($user->isStaff() && $submission->user_id !== $user->id) {
            abort(403, 'Anda tidak diijinkan mengakses berkas ini.');
        }

        if (!Storage::disk('private')->exists($file->file_path)) {
            abort(404, 'Berkas fisik tidak ditemukan di sistem penyimpanan.');
        }

        // Return secure download
        return Storage::disk('private')->download($file->file_path, $file->file_name);
    }
}
