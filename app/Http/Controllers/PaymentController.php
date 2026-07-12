<?php

namespace App\Http\Controllers;

use App\Models\ExpenseRequest;
use App\Models\Payment;
use App\Services\ActivityLogger;
use App\Services\ExpenseApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $expenseApprovalService;

    public function __construct(ExpenseApprovalService $expenseApprovalService)
    {
        $this->expenseApprovalService = $expenseApprovalService;
    }

    public function index()
    {
        // 1. Waiting validation (status: Waiting Finance)
        $waitingValidation = ExpenseRequest::where('status', 'Waiting Finance')
            ->with(['user.department', 'category'])
            ->orderBy('created_at', 'asc')
            ->get();

        // 2. Waiting payment (status: Approved)
        $waitingPayment = ExpenseRequest::where('status', 'Approved')
            ->with(['user.department', 'category'])
            ->orderBy('created_at', 'asc')
            ->get();

        // 3. Paid history
        $payments = Payment::with(['expenseRequest.user.department', 'expenseRequest.category', 'paidBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payments.index', compact('waitingValidation', 'waitingPayment', 'payments'));
    }

    public function show(ExpenseRequest $expenseRequest)
    {
        $expenseRequest->load(['category', 'department', 'attachments', 'approvalHistories.user', 'payment']);
        
        return view('payments.show', compact('expenseRequest'));
    }

    public function validateRequest(Request $request, ExpenseRequest $expenseRequest)
    {
        $request->validate([
            'action' => ['required', 'in:Approve,Reject'],
            'notes' => ['required_if:action,Reject', 'nullable', 'string'],
        ], [
            'action.required' => 'Keputusan validasi wajib dipilih.',
            'notes.required_if' => 'Catatan penolakan wajib diisi jika pengajuan ditolak.',
        ]);

        try {
            $action = $request->input('action');
            $notes = $request->input('notes');

            // Finance approves (meaning validates budget) or rejects
            $this->expenseApprovalService->processApproval($expenseRequest, $action, $notes, Auth::user());

            $message = $action === 'Approve' 
                ? 'Pengajuan transaksi berhasil divalidasi dan siap dibayar.' 
                : 'Pengajuan transaksi ditolak oleh Finance.';

            return redirect()->route('payments.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses validasi: ' . $e->getMessage());
        }
    }

    public function pay(Request $request, ExpenseRequest $expenseRequest)
    {
        if ($expenseRequest->status !== 'Approved') {
            return redirect()->back()->with('error', 'Pengajuan harus divalidasi terlebih dahulu sebelum diproses pembayarannya.');
        }

        $request->validate([
            'payment_number' => ['required', 'string', 'max:100', 'unique:payments,payment_number'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ], [
            'payment_number.required' => 'Nomor pembayaran wajib diisi.',
            'payment_number.unique' => 'Nomor pembayaran sudah digunakan.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
        ]);

        try {
            DB::transaction(function () use ($expenseRequest, $request) {
                // 1. Create payment record
                Payment::create([
                    'expense_request_id' => $expenseRequest->id,
                    'payment_number' => $request->input('payment_number'),
                    'payment_date' => $request->input('payment_date'),
                    'amount' => $expenseRequest->amount,
                    'paid_by' => Auth::id(),
                    'notes' => $request->input('notes'),
                ]);

                // 2. Update status of request to Paid
                $expenseRequest->update([
                    'status' => 'Paid',
                ]);

                // 3. Log approval history for payment
                $expenseRequest->approvalHistories()->create([
                    'user_id' => Auth::id(),
                    'role' => Auth::user()->role->display_name,
                    'action' => 'Pay',
                    'notes' => 'Pembayaran telah diproses dengan No. ' . $request->input('payment_number'),
                ]);

                // 4. Activity log
                ActivityLogger::log(
                    'Payment',
                    "Memproses pembayaran pengajuan {$expenseRequest->request_number} dengan No. Pembayaran {$request->input('payment_number')} senilai Rp " . number_format($expenseRequest->amount, 0, ',', '.')
                );
            });

            return redirect()->route('payments.index')->with('success', 'Pembayaran berhasil diproses. Status pengajuan diubah menjadi Paid.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
