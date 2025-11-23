<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PaymentTransaction;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Process payment (mock for now)
     */
    public function processPayment(int $invoiceId, int $paymentMethodId, float $amount, array $metadata = []): PaymentTransaction
    {
        return DB::transaction(function () use ($invoiceId, $paymentMethodId, $amount, $metadata) {
            $invoice = Invoice::findOrFail($invoiceId);
            $paymentMethod = PaymentMethod::findOrFail($paymentMethodId);

            // Mock payment processing - always succeeds for now
            $transaction = PaymentTransaction::create([
                'invoice_id' => $invoiceId,
                'payment_method_id' => $paymentMethodId,
                'amount' => $amount,
                'status' => 'success', // Mock: always success
                'reference_code' => 'MOCK-' . strtoupper(uniqid()),
                'metadata' => array_merge($metadata, [
                    'method' => $paymentMethod->type,
                    'processed_at' => now()->toIso8601String(),
                ]),
                'processed_at' => now(),
            ]);

            // Update invoice status
            $invoice->updateStatus();

            // Award gamification points for payment
            if ($transaction->status === 'success') {
                try {
                    $gamificationService = app(\App\Services\GamificationService::class);
                    $enrollment = $invoice->enrollment;
                    if ($enrollment && $enrollment->student) {
                        $gamificationService->awardPointsForEvent(
                            $enrollment->student,
                            'payment_made',
                            'payment_transactions',
                            $transaction->id,
                            [
                                'invoice_id' => $invoiceId,
                                'amount' => $amount,
                                'payment_method_id' => $paymentMethodId,
                            ]
                        );
                    }
                } catch (\Exception $e) {
                    // Log but don't fail payment if gamification fails
                    \Illuminate\Support\Facades\Log::warning('Gamification failed for payment', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $transaction;
        });
    }

    /**
     * Mark invoice as paid manually (admin)
     */
    public function markInvoiceAsPaid(int $invoiceId, int $paymentMethodId, float $amount, string $referenceCode = null): PaymentTransaction
    {
        return DB::transaction(function () use ($invoiceId, $paymentMethodId, $amount, $referenceCode) {
            $transaction = PaymentTransaction::create([
                'invoice_id' => $invoiceId,
                'payment_method_id' => $paymentMethodId,
                'amount' => $amount,
                'status' => 'success',
                'reference_code' => $referenceCode ?? 'MANUAL-' . now()->format('YmdHis'),
                'metadata' => [
                    'marked_by_admin' => true,
                ],
                'processed_at' => now(),
            ]);

            $invoice = Invoice::findOrFail($invoiceId);
            $invoice->updateStatus();

            // Award gamification points for payment
            if ($transaction->status === 'success') {
                try {
                    $gamificationService = app(\App\Services\GamificationService::class);
                    $enrollment = $invoice->enrollment;
                    if ($enrollment && $enrollment->student) {
                        $gamificationService->awardPointsForEvent(
                            $enrollment->student,
                            'payment_made',
                            'payment_transactions',
                            $transaction->id,
                            [
                                'invoice_id' => $invoiceId,
                                'amount' => $amount,
                                'payment_method_id' => $paymentMethodId,
                            ]
                        );
                    }
                } catch (\Exception $e) {
                    // Log but don't fail payment if gamification fails
                    \Illuminate\Support\Facades\Log::warning('Gamification failed for payment', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $transaction;
        });
    }
}

