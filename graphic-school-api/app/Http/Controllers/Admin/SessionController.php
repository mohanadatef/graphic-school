<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $query = Session::with('course:id,title');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->integer('course_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        return response()->json(
            $query->orderBy('session_date')->paginate($request->integer('per_page', 10))
        );
    }

    public function show(Session $session)
    {
        return response()->json($session->load('course'));
    }

    public function update(Request $request, Session $session)
    {
        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'session_date' => ['sometimes', 'required', 'date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'status' => ['nullable', 'in:scheduled,completed,cancelled'],
            'note' => ['nullable', 'string'],
        ]);

        $session->update($data);

        return response()->json($session);
    }

    public function destroy(Session $session)
    {
        $session->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
