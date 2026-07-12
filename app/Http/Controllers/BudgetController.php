<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BudgetController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:Finance|Director|Manager', only: ['index']),
            new Middleware('role:Finance', except: ['index']),
        ];
    }

    /**
     * Display a listing of budgets.
     */
    public function index()
    {
        $budgets = Budget::with('category')->orderBy('fiscal_year', 'desc')->paginate(10);
        $categories = Category::all();
        return view('budgets.index', compact('budgets', 'categories'));
    }

    /**
     * Store a newly allocated budget.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'fiscal_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'limit_amount' => ['required', 'numeric', 'min:0'],
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
            'fiscal_year.required' => 'Tahun anggaran wajib diisi.',
            'limit_amount.required' => 'Nominal limit anggaran wajib diisi.',
            'limit_amount.numeric' => 'Nominal limit harus berupa angka.',
        ]);

        // Check duplicate
        $exists = Budget::where('category_id', $validated['category_id'])
            ->where('fiscal_year', $validated['fiscal_year'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Anggaran untuk kategori ini di tahun fiskal yang sama sudah terdaftar.');
        }

        Budget::create([
            'category_id' => $validated['category_id'],
            'fiscal_year' => $validated['fiscal_year'],
            'limit_amount' => $validated['limit_amount'],
            'spent_amount' => 0.00,
            'remaining_amount' => $validated['limit_amount'],
        ]);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Anggaran baru berhasil dialokasikan.');
    }

    /**
     * Update the limit amount of a budget.
     */
    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'limit_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $limit = (float) $validated['limit_amount'];
        $spent = (float) $budget->spent_amount;

        if ($limit < $spent) {
            return back()->with('error', 'Limit anggaran baru tidak boleh lebih kecil dari dana yang telah terpakai (Rp ' . number_format($spent, 0, ',', '.') . ').');
        }

        $budget->update([
            'limit_amount' => $limit,
            'remaining_amount' => $limit - $spent,
        ]);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Limit anggaran berhasil diperbarui.');
    }

    /**
     * Remove the specified budget.
     */
    public function destroy(Budget $budget)
    {
        if ((float) $budget->spent_amount > 0) {
            return back()->with('error', 'Anggaran tidak dapat dihapus karena dana telah terpakai.');
        }

        $budget->delete();

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Alokasi anggaran berhasil dihapus.');
    }
}
