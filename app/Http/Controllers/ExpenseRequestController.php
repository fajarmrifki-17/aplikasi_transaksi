<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\ExpenseCategory;
use App\Models\ExpenseRequest;
use App\Services\ExpenseRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseRequestController extends Controller
{
    protected $expenseRequestService;

    public function __construct(ExpenseRequestService $expenseRequestService)
    {
        $this->expenseRequestService = $expenseRequestService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = ExpenseRequest::where('user_id', $user->id);

        // Search vendor or description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $expenseRequests = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('expense-requests.index', compact('expenseRequests'));
    }

    public function create()
    {
        $categories = ExpenseCategory::all();
        return view('expense-requests.create', compact('categories'));
    }

    public function store(StoreExpenseRequest $request)
    {
        try {
            $attachmentFile = $request->file('attachment');
            $this->expenseRequestService->createRequest($request->validated(), $attachmentFile, Auth::user());
            
            return redirect()->route('expense-requests.index')
                ->with('success', 'Pengajuan transaksi berhasil dibuat sebagai Draft.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat pengajuan: ' . $e->getMessage());
        }
    }

    public function show(ExpenseRequest $expenseRequest)
    {
        // Staff can only view their own requests
        if ($expenseRequest->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat pengajuan ini.');
        }

        $expenseRequest->load(['category', 'department', 'attachments', 'approvalHistories.user', 'payment.paidBy']);
        
        return view('expense-requests.show', compact('expenseRequest'));
    }

    public function edit(ExpenseRequest $expenseRequest)
    {
        if ($expenseRequest->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah pengajuan ini.');
        }

        if ($expenseRequest->status !== 'Draft') {
            return redirect()->route('expense-requests.show', $expenseRequest)
                ->with('warning', 'Hanya pengajuan berstatus Draft yang dapat diubah.');
        }

        $categories = ExpenseCategory::all();
        return view('expense-requests.edit', compact('expenseRequest', 'categories'));
    }

    public function update(StoreExpenseRequest $request, ExpenseRequest $expenseRequest)
    {
        if ($expenseRequest->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah pengajuan ini.');
        }

        try {
            $attachmentFile = $request->file('attachment');
            $this->expenseRequestService->updateRequest($expenseRequest, $request->validated(), $attachmentFile);

            return redirect()->route('expense-requests.show', $expenseRequest)
                ->with('success', 'Pengajuan transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pengajuan: ' . $e->getMessage());
        }
    }

    public function destroy(ExpenseRequest $expenseRequest)
    {
        if ($expenseRequest->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus pengajuan ini.');
        }

        if ($expenseRequest->status !== 'Draft') {
            return redirect()->route('expense-requests.show', $expenseRequest)
                ->with('warning', 'Hanya pengajuan berstatus Draft yang dapat dihapus.');
        }

        try {
            // Delete attachments physically
            foreach ($expenseRequest->attachments as $attachment) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_path);
            }
            $expenseRequest->delete();

            return redirect()->route('expense-requests.index')
                ->with('success', 'Pengajuan Draft berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus pengajuan: ' . $e->getMessage());
        }
    }

    public function submit(ExpenseRequest $expenseRequest)
    {
        if ($expenseRequest->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengirimkan pengajuan ini.');
        }

        try {
            $this->expenseRequestService->submitRequest($expenseRequest, Auth::user());

            return redirect()->route('expense-requests.show', $expenseRequest)
                ->with('success', 'Pengajuan transaksi berhasil dikirim untuk proses approval.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengirim pengajuan: ' . $e->getMessage());
        }
    }
}
