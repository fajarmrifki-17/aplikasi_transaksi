<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fiscalYear = 2026;

        $budgets = [
            'PO-PROD' => 200000000.00,       // 200 Million
            'OPERASIONAL' => 50000000.00,     // 50 Million
            'MARKETING' => 100000000.00,      // 100 Million
            'TRAVEL' => 30000000.00,          // 30 Million
        ];

        foreach ($budgets as $code => $limit) {
            $category = Category::where('code', $code)->first();
            if ($category) {
                Budget::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'fiscal_year' => $fiscalYear,
                    ],
                    [
                        'limit_amount' => $limit,
                        'spent_amount' => 0.00,
                        'remaining_amount' => $limit,
                    ]
                );
            }
        }
    }
}
