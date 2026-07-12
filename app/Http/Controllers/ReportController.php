<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Repositories\CategoryRepository;
use App\Repositories\SubmissionRepository;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReportController extends Controller implements HasMiddleware
{
    protected SubmissionRepository $submissionRepository;
    protected CategoryRepository $categoryRepository;

    public static function middleware(): array
    {
        return [
            'role:Finance|Director|Manager',
        ];
    }

    public function __construct(
        SubmissionRepository $submissionRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->submissionRepository = $submissionRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display report filters and search result list.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'status', 'start_date', 'end_date']);
        $submissions = $this->submissionRepository->getPaginatedForFinance(15, $filters);
        $categories = $this->categoryRepository->all();

        return view('reports.index', compact('submissions', 'categories'));
    }

    /**
     * Export submissions report to Excel-compatible CSV.
     */
    public function exportCsv(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'status', 'start_date', 'end_date']);
        
        // Fetch all submissions matching filters (without pagination)
        $query = Submission::with(['user', 'category', 'payment']);
        
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

        $submissions = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=Laporan_Pengajuan_Transaksi_" . date('Ymd_His') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Nomor Pengajuan', 
            'Tanggal', 
            'Pemohon', 
            'Kategori', 
            'Deskripsi', 
            'Nominal', 
            'Status', 
            'Tanggal Pembayaran', 
            'No Referensi Bank'
        ];

        $callback = function() use($submissions, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM to make it open correctly in Excel with characters like currency symbols
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $columns, ';'); // Excel uses semicolon as delimiter in many locales

            foreach ($submissions as $sub) {
                fputcsv($file, [
                    $sub->submission_number,
                    $sub->submission_date ? $sub->submission_date->format('Y-m-d') : '-',
                    $sub->user ? $sub->user->name : '-',
                    $sub->category ? $sub->category->name : '-',
                    $sub->description,
                    $sub->requested_amount,
                    $sub->status,
                    $sub->payment && $sub->payment->payment_date ? $sub->payment->payment_date->format('Y-m-d') : '-',
                    $sub->payment ? $sub->payment->reference_number : '-',
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export submissions report to a beautifully formatted PDF.
     */
    public function printPdf(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'status', 'start_date', 'end_date']);

        $query = Submission::with(['user', 'category', 'payment']);

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

        $submissions = $query->orderBy('created_at', 'desc')->get();
        $totalAmount = $submissions->sum('requested_amount');

        $pdf = Pdf::loadView('reports.pdf', compact('submissions', 'totalAmount', 'filters'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Pengajuan_Transaksi_' . date('Ymd_His') . '.pdf');
    }
}
