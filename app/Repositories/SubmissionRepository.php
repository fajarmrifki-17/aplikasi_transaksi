<?php

namespace App\Repositories;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SubmissionRepository
{
    /**
     * Get paginated submissions with filtering for a specific user (Staff).
     */
    public function getPaginatedForUser(User $user, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Submission::with(['category', 'submissionFiles'])
            ->where('user_id', $user->id);

        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get paginated submissions waiting for approval by specific roles.
     */
    public function getPaginatedForApprover(string $role, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Submission::with(['user', 'category', 'submissionFiles', 'approvals']);

        // Filter based on role
        if ($role === 'Supervisor') {
            $query->where('status', 'Waiting Supervisor Approval');
        } elseif ($role === 'Manager') {
            $query->where('status', 'Waiting Manager Approval');
        } elseif ($role === 'Director') {
            $query->where('status', 'Waiting Director Approval');
        } else {
            // If unknown role, return empty paginator
            $query->whereRaw('1 = 0');
        }

        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'asc')->paginate($perPage);
    }

    /**
     * Get paginated submissions for Finance validation and payment.
     */
    public function getPaginatedForFinance(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Submission::with(['user', 'category', 'submissionFiles', 'approvals', 'payment']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find submission by ID with relations.
     */
    public function find(int $id): ?Submission
    {
        return Submission::with(['user', 'category', 'submissionFiles', 'approvals.user', 'payment.paidBy'])->find($id);
    }

    /**
     * Get dynamic dashboard stats based on user role.
     */
    public function getStats(User $user): array
    {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'paid' => 0,
        ];

        if ($user->isStaff()) {
            $stats['total'] = Submission::where('user_id', $user->id)->count();
            $stats['pending'] = Submission::where('user_id', $user->id)
                ->whereIn('status', ['Submitted', 'Waiting Supervisor Approval', 'Waiting Manager Approval', 'Waiting Director Approval'])
                ->count();
            $stats['approved'] = Submission::where('user_id', $user->id)
                ->whereIn('status', ['Waiting Finance', 'Paid'])
                ->count();
            $stats['rejected'] = Submission::where('user_id', $user->id)->where('status', 'Rejected')->count();
            $stats['paid'] = Submission::where('user_id', $user->id)->where('status', 'Paid')->count();
        } else {
            // General system stats for supervisors, managers, directors, finance
            $stats['total'] = Submission::count();
            $stats['pending'] = Submission::whereIn('status', ['Submitted', 'Waiting Supervisor Approval', 'Waiting Manager Approval', 'Waiting Director Approval', 'Waiting Finance'])->count();
            $stats['approved'] = Submission::whereIn('status', ['Waiting Finance', 'Paid'])->count();
            $stats['rejected'] = Submission::where('status', 'Rejected')->count();
            $stats['paid'] = Submission::where('status', 'Paid')->count();
        }

        return $stats;
    }

    /**
     * Get monthly payment statistics for Chart.js.
     */
    public function getMonthlyStatistics(int $year): array
    {
        $data = Submission::select(
                DB::raw('MONTH(submission_date) as month'),
                DB::raw('SUM(requested_amount) as total_requested'),
                DB::raw('SUM(CASE WHEN status = "Paid" THEN requested_amount ELSE 0 END) as total_paid')
            )
            ->whereYear('submission_date', $year)
            ->groupBy(DB::raw('MONTH(submission_date)'))
            ->orderBy('month')
            ->get();

        $monthlyStats = array_fill(1, 12, ['requested' => 0, 'paid' => 0]);
        foreach ($data as $stat) {
            $monthlyStats[$stat->month] = [
                'requested' => (float)$stat->total_requested,
                'paid' => (float)$stat->total_paid,
            ];
        }

        return array_values($monthlyStats);
    }

    /**
     * Apply common search and filter logic.
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('submission_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('submission_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('submission_date', '<=', $filters['end_date']);
        }
    }
}
