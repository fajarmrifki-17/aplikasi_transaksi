<?php

namespace App\Repositories;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Collection;

class BudgetRepository
{
    public function all(): Collection
    {
        return Budget::with('category')->get();
    }

    public function find(int $id): ?Budget
    {
        return Budget::with('category')->find($id);
    }

    public function findByCategoryAndYear(int $categoryId, int $year): ?Budget
    {
        return Budget::where('category_id', $categoryId)
            ->where('fiscal_year', $year)
            ->first();
    }

    public function getRemainingBudget(int $categoryId, int $year): float
    {
        $budget = $this->findByCategoryAndYear($categoryId, $year);
        return $budget ? (float) $budget->remaining_amount : 0.0;
    }
}
