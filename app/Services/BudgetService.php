<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Budget;
use Illuminate\Support\Facades\DB;

class BudgetService
{
    /**
     * Check if there is sufficient budget for the category in the current year.
     */
    public function hasSufficientBudget(Category $category, float $amount, int $year): bool
    {
        $budget = Budget::where('category_id', $category->id)
            ->where('fiscal_year', $year)
            ->first();

        if (!$budget) {
            return false; // No budget defined means no spending allowed
        }

        return (float) $budget->remaining_amount >= $amount;
    }

    /**
     * Deduct budget for a category and fiscal year.
     * Uses DB transactions with pessimistic locking to prevent race conditions.
     */
    public function deductBudget(Category $category, float $amount, int $year): void
    {
        DB::transaction(function () use ($category, $amount, $year) {
            $budget = Budget::where('category_id', $category->id)
                ->where('fiscal_year', $year)
                ->lockForUpdate()
                ->first();

            if (!$budget) {
                throw new \Exception("Anggaran untuk kategori {$category->name} tahun {$year} tidak ditemukan.");
            }

            if ((float) $budget->remaining_amount < $amount) {
                throw new \Exception("Anggaran tidak mencukupi. Sisa anggaran: Rp " . number_format($budget->remaining_amount, 0, ',', '.'));
            }

            $newSpent = (float) $budget->spent_amount + $amount;
            $newRemaining = (float) $budget->limit_amount - $newSpent;

            $budget->update([
                'spent_amount' => $newSpent,
                'remaining_amount' => $newRemaining,
            ]);
        });
    }
}
