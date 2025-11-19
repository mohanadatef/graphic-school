<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        return response()->json(Testimonial::latest()->paginate(30));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'is_approved' => ['required', 'boolean'],
        ]);

        $testimonial->update($data);

        return response()->json($testimonial);
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
