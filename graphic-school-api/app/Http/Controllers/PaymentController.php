<?php

namespace App\Http\Controllers;

use App\Support\Controllers\BaseController;
use App\Models\Payment;
use Modules\LMS\Enrollments\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * CHANGE-004: Payment Timeline
 */
class PaymentController extends BaseController
{
    /**
     * Get payments for authenticated student
     */
    public function studentPayments(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = Payment::where('student_id', $user->id)
            ->with(['course', 'enrollment'])
            ->orderBy('payment_date', 'desc');

        // Filter by course
        if ($request->has('course_id')) {
            $query->where('course_id', $request->input('course_id'));
        }

        // Filter by enrollment
        if ($request->has('enrollment_id')) {
            $query->where('enrollment_id', $request->input('enrollment_id'));
        }

        $payments = $query->paginate($request->input('per_page', 15));

        // Calculate totals
        $totalPaid = Payment::where('student_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $totalRemaining = Payment::where('student_id', $user->id)
            ->sum('remaining_amount');

        return $this->paginated($payments, 'Payments retrieved successfully', 200, [
            'totals' => [
                'total_paid' => (float) $totalPaid,
                'total_remaining' => (float) $totalRemaining,
            ],
        ]);
    }

    /**
     * Get all payments (Admin only)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['student', 'course', 'enrollment', 'creator'])
            ->orderBy('payment_date', 'desc');

        // Filter by student
        if ($request->has('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        // Filter by course
        if ($request->has('course_id')) {
            $query->where('course_id', $request->input('course_id'));
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('payment_date', '>=', $request->input('from_date'));
        }
        if ($request->has('to_date')) {
            $query->where('payment_date', '<=', $request->input('to_date'));
        }

        $payments = $query->paginate($request->input('per_page', 15));

        return $this->paginated($payments, 'Payments retrieved successfully');
    }

    /**
     * Create new payment (Admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'payment_date' => 'required|date',
            'status' => 'nullable|in:pending,completed,failed,refunded',
        ]);

        $enrollment = Enrollment::with('course')->findOrFail($validated['enrollment_id']);

        // Calculate remaining amount
        $totalPaid = Payment::where('enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->sum('amount');
        
        $newTotalPaid = $totalPaid + $validated['amount'];
        $remainingAmount = max(0, $enrollment->total_amount - $newTotalPaid);

        $payment = Payment::create([
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
            'course_id' => $enrollment->course_id,
            'amount' => $validated['amount'],
            'remaining_amount' => $remainingAmount,
            'payment_method' => $validated['payment_method'] ?? null,
            'payment_reference' => $validated['payment_reference'] ?? null,
            'description' => $validated['description'] ?? null,
            'payment_date' => $validated['payment_date'],
            'status' => $validated['status'] ?? 'completed',
            'created_by' => $request->user()->id,
        ]);

        // Update enrollment payment status
        $enrollment->update([
            'paid_amount' => $newTotalPaid,
            'payment_status' => $remainingAmount > 0 ? 'partially_paid' : 'paid',
        ]);

        return $this->created($payment, 'Payment created successfully');
    }

    /**
     * Update payment (Admin only)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'payment_date' => 'sometimes|date',
            'status' => 'sometimes|in:pending,completed,failed,refunded',
        ]);

        $payment->update($validated);

        // Recalculate remaining amount if amount changed
        if (isset($validated['amount'])) {
            $totalPaid = Payment::where('enrollment_id', $payment->enrollment_id)
                ->where('status', 'completed')
                ->sum('amount');
            
            $enrollment = $payment->enrollment;
            $remainingAmount = max(0, $enrollment->total_amount - $totalPaid);
            
            $payment->update(['remaining_amount' => $remainingAmount]);
            
            $enrollment->update([
                'paid_amount' => $totalPaid,
                'payment_status' => $remainingAmount > 0 ? 'partially_paid' : 'paid',
            ]);
        }

        return $this->success($payment, 'Payment updated successfully');
    }

    /**
     * Get payment reports (Admin only)
     */
    public function reports(Request $request): JsonResponse
    {
        $query = Payment::query();

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('payment_date', '>=', $request->input('from_date'));
        }
        if ($request->has('to_date')) {
            $query->where('payment_date', '<=', $request->input('to_date'));
        }

        // Filter by course
        if ($request->has('course_id')) {
            $query->where('course_id', $request->input('course_id'));
        }

        $totalPaid = (float) $query->where('status', 'completed')->sum('amount');
        $totalPending = (float) $query->where('status', 'pending')->sum('amount');
        $totalRemaining = (float) $query->sum('remaining_amount');
        $paymentCount = $query->count();

        // Payments by course
        $byCourse = Payment::select('course_id', DB::raw('SUM(amount) as total'))
            ->where('status', 'completed')
            ->groupBy('course_id')
            ->with('course:id,title')
            ->get();

        return $this->success([
            'summary' => [
                'total_paid' => $totalPaid,
                'total_pending' => $totalPending,
                'total_remaining' => $totalRemaining,
                'payment_count' => $paymentCount,
            ],
            'by_course' => $byCourse,
        ], 'Payment reports retrieved successfully');
    }
}
