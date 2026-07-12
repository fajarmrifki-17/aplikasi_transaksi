<?php

use App\Http\Controllers\Api\BudgetApiController;
use App\Http\Controllers\Api\SubmissionApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public login route to obtain API tokens
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Kredensial login tidak valid.'
        ], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'status' => 'success',
        'token' => $token,
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roles->first()->name ?? null
        ]
    ]);
});

// Authenticated API routes
Route::middleware('auth:sanctum')->name('api.')->group(function () {
    // Submissions CRUD & Workflow
    Route::post('/submissions/{id}/submit', [SubmissionApiController::class, 'submit'])->name('submissions.submit');
    Route::apiResource('submissions', SubmissionApiController::class);

    // Budget Information
    Route::get('/budgets', [BudgetApiController::class, 'index'])->name('budgets.index');
    Route::get('/budgets/{categoryId}/{year}', [BudgetApiController::class, 'show'])->name('budgets.show');
});
