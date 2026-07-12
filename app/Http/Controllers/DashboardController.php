<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Budget;
use App\Repositories\SubmissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected SubmissionRepository $submissionRepository;

    public function __construct(SubmissionRepository $submissionRepository)
    {
        $this->submissionRepository = $submissionRepository;
    }

    public function index()
    {
        $user = Auth::user();
        
        // 1. Get stats
        $stats = $this->submissionRepository->getStats($user);

        // 2. Get active budgets for the current year
        $currentYear = (int) date('Y');
        $budgets = Budget::with('category')
            ->where('fiscal_year', $currentYear)
            ->get();

        // 3. Get monthly statistics (requested vs paid)
        $chartData = $this->submissionRepository->getMonthlyStatistics($currentYear);

        // 4. Get recent activities (all users can see recent activities, scoped or system-wide)
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // 5. Get recent user submissions (for staff dashboard)
        $recentSubmissions = [];
        if ($user->isStaff()) {
            $recentSubmissions = $user->submissions()
                ->with('category')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        // 6. Get pending approvals count for the logged-in user
        $pendingApprovals = [];
        if ($user->hasAnyRole(['Supervisor', 'Manager', 'Director'])) {
            $role = '';
            if ($user->isSupervisor()) $role = 'Supervisor';
            elseif ($user->isManager()) $role = 'Manager';
            elseif ($user->isDirector()) $role = 'Director';
            
            $pendingApprovals = $this->submissionRepository->getPaginatedForApprover($role, 5)->items();
        }

        // 7. Get pending payments for Finance
        $pendingPayments = [];
        if ($user->isFinance()) {
            $pendingPayments = $this->submissionRepository->getPaginatedForFinance(5, ['status' => 'Waiting Finance'])->items();
        }

        return view('dashboard', compact(
            'stats',
            'budgets',
            'chartData',
            'recentActivities',
            'recentSubmissions',
            'pendingApprovals',
            'pendingPayments'
        ));
    }
}
