<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // 1. Dashboard (Dynamic cards & stats)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 3. Submissions (Staff / Applicants)
    Route::get('submissions/files/{file}', [SubmissionController::class, 'downloadFile'])->name('submissions.download');
    Route::post('submissions/{submission}/submit', [SubmissionController::class, 'submit'])->name('submissions.submit');
    Route::resource('submissions', SubmissionController::class);

    // 4. Approvals (Supervisor, Manager, Director)
    Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('approvals/{submission}', [ApprovalController::class, 'show'])->name('approvals.show');
    Route::post('approvals/{submission}/action', [ApprovalController::class, 'action'])->name('approvals.action');

    // 5. Payments (Finance)
    Route::get('payments', [FinanceController::class, 'index'])->name('payments.index');
    Route::get('payments/{submission}', [FinanceController::class, 'show'])->name('payments.show');
    Route::post('payments/{submission}/pay', [FinanceController::class, 'pay'])->name('payments.pay');

    // 6. Reports (Finance, Director, Manager)
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('reports/export/pdf', [ReportController::class, 'printPdf'])->name('reports.export.pdf');

    // 7. Categories & Budgets (Finance, Director, Manager)
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('budgets', BudgetController::class)->except(['create', 'show', 'edit']);

    // 8. User RBAC Settings (Finance)
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
});

require __DIR__.'/auth.php';
