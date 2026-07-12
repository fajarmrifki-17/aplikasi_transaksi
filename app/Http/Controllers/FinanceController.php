<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Submission;
use App\Repositories\CategoryRepository;
use App\Repositories\SubmissionRepository;
use App\Services\PaymentService;
use App\Services\BudgetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FinanceController extends Controller implements HasMiddleware
{
    protected SubmissionRepository $submissionRepository;
    protected CategoryRepository $categoryRepository;
    protected PaymentService $paymentService;
    protected BudgetService $budgetService;

    public static function middleware(): array
    {
        return [
            'role:Finance',
        ];
    }

    public function __construct(
        SubmissionRepository $submissionRepository,
        CategoryRepository $categoryRepository,
        PaymentService $paymentService,
        BudgetService $budgetService
    ) {
        $this->submissionRepository = $submissionRepository;
        $this->categoryRepository = $categoryRepository;
        $this->paymentService = $paymentService;
        $this->budgetService = $budgetService;
    }

    /**
     * Display submissions requiring Finance action.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'status', 'start_date', 'end_date']);
        
        // Default filter status to 'Waiting Finance' if not specified
        if (!isset($filters['status'])) {
            $filters['status'] = 'Waiting Finance';
        }

        $submissions = $this->submissionRepository->getPaginatedForFinance(10, $filters);
        $categories = $this->categoryRepository->all();

        return view('payments.index', compact('submissions', 'categories'));
    }

    /**
     * Display submission payment validation panel.
     */
    public function show(Submission $submission)
    {
        $submission = $this->submissionRepository->find($submission->id);
        
        $year = date('Y', strtotime($submission->submission_date));
        $hasBudget = $this->budgetService->hasSufficientBudget($submission->category, (float)$submission->requested_amount, $year);

        return view('payments.show', compact('submission', 'hasBudget'));
    }

    /**
     * Submit payment disbursement details.
     */
    public function pay(PaymentRequest $request, Submission $submission)
    {
        try {
            $this->paymentService->processPayment(
                $submission,
                $request->validated(),
                Auth::user()
            );

            return redirect()
                ->route('payments.index')
                ->with('success', "Pembayaran untuk pengajuan {$submission->submission_number} berhasil diproses.");
        } catch (\Exception $e) {
            // Check if the submission was rejected due to budget issues during execution
            $submission->refresh();
            if ($submission->status === 'Rejected') {
                return redirect()
                    ->route('payments.index')
                    ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
            }

            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
