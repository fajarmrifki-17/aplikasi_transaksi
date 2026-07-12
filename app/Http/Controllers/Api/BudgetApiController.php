<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\BudgetRepository;
use Illuminate\Http\Request;

class BudgetApiController extends Controller
{
    protected BudgetRepository $budgetRepository;

    public function __construct(BudgetRepository $budgetRepository)
    {
        $this->budgetRepository = $budgetRepository;
    }

    /**
     * Get active budgets.
     */
    public function index()
    {
        $budgets = $this->budgetRepository->all();
        
        return response()->json([
            'status' => 'success',
            'data' => $budgets
        ]);
    }

    /**
     * Get active budget for a category and fiscal year.
     */
    public function show(int $categoryId, int $year)
    {
        $budget = $this->budgetRepository->findByCategoryAndYear($categoryId, $year);

        if (!$budget) {
            return response()->json([
                'status' => 'error',
                'message' => 'Budget not found for category in this year.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $budget
        ]);
    }
}
