<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with(['student', 'course']);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->get('payment_status'));
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->integer('course_id'));
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->integer('student_id'));
        }

        return response()->json(
            $query->orderByDesc('created_at')->paginate($request->integer('per_page', 10))
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'payment_status' => ['required', 'in:not_paid,partial,paid'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'can_attend' => ['nullable', 'boolean'],
        ]);

        $exists = Enrollment::where('student_id', $data['student_id'])
            ->where('course_id', $data['course_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Student already enrolled'], 422);
        }

        $enrollment = Enrollment::create($data);

        return response()->json($enrollment->load('student', 'course'), 201);
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $data = $request->validate([
            'payment_status' => ['nullable', 'in:not_paid,partial,paid'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:pending,approved,rejected'],
            'can_attend' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string'],
        ]);

        if (isset($data['status']) && $data['status'] === 'approved') {
            $data['can_attend'] = true;
            $data['approved_by'] = $request->user()->id;
            $data['approved_at'] = now();
        }

        $enrollment->update($data);

        return response()->json($enrollment->fresh()->load('student', 'course'));
    }
}
