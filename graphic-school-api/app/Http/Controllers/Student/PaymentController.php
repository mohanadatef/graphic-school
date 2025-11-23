<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PaymentService;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {
    }

    /**
     * Get student invoices
     */
    public function invoices(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;

        $invoices = Invoice::whereHas('enrollment', function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        })
        ->with(['enrollment.program', 'items', 'transactions.paymentMethod'])
        ->orderByDesc('created_at')
        ->paginate($request->get('per_page', 15));

        return ApiResponse::success($invoices, 'Invoices retrieved successfully');
    }

    /**
     * Show invoice
     */
    public function showInvoice(int $id, Request $request): JsonResponse
    {
        $studentId = $request->user()->id;

        $invoice = Invoice::whereHas('enrollment', function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        })
        ->with(['enrollment.program', 'items', 'transactions.paymentMethod'])
        ->findOrFail($id);

        return ApiResponse::success($invoice, 'Invoice retrieved successfully');
    }

    /**
     * Process payment (mock)
     */
    public function pay(Request $request): JsonResponse
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $studentId = $request->user()->id;

        // Verify invoice belongs to student
        $invoice = Invoice::whereHas('enrollment', function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        })->findOrFail($request->invoice_id);

        $transaction = $this->paymentService->processPayment(
            $request->invoice_id,
            $request->payment_method_id,
            $request->amount
        );

        return ApiResponse::success($transaction->load(['invoice', 'paymentMethod']), 'Payment processed successfully');
    }
}

