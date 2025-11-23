<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PaymentService;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {
    }

    /**
     * List invoices
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['enrollment.student', 'enrollment.program', 'items', 'transactions']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('enrollment_id')) {
            $query->where('enrollment_id', $request->enrollment_id);
        }

        $invoices = $query->orderByDesc('created_at')->paginate($request->get('per_page', 15));

        return ApiResponse::success($invoices, 'Invoices retrieved successfully');
    }

    /**
     * Show invoice
     */
    public function show(int $id): JsonResponse
    {
        $invoice = Invoice::with(['enrollment.student', 'enrollment.program', 'items', 'transactions.paymentMethod'])
            ->findOrFail($id);

        return ApiResponse::success($invoice, 'Invoice retrieved successfully');
    }

    /**
     * Mark invoice as paid
     */
    public function markPaid(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'reference_code' => 'nullable|string|max:255',
        ]);

        $invoice = Invoice::findOrFail($id);
        $transaction = $this->paymentService->markInvoiceAsPaid(
            $id,
            $request->payment_method_id,
            $request->amount,
            $request->input('reference_code')
        );

        return ApiResponse::success($invoice->fresh()->load(['items', 'transactions']), 'Invoice marked as paid successfully');
    }
}

