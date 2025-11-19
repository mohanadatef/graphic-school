<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with([
            'session.course:id,title',
            'student:id,name,email',
        ]);

        if ($request->filled('course_id')) {
            $query->whereHas('session', function ($q) use ($request) {
                $q->where('course_id', $request->query('course_id'));
            });
        }

        if ($request->filled('session_id')) {
            $query->where('session_id', $request->query('session_id'));
        }

        return response()->json(
            $query->orderByDesc('created_at')->paginate(50)
        );
    }
}
