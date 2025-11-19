<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Testimonial\UpdateTestimonialRequest;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use App\Services\TestimonialService;

class TestimonialController extends Controller
{
    public function __construct(private TestimonialService $testimonialService)
    {
    }

    public function index()
    {
        return TestimonialResource::collection(
            $this->testimonialService->paginate()
        );
    }

    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial)
    {
        $testimonial = $this->testimonialService->update($testimonial, $request->validated());

        return TestimonialResource::make($testimonial);
    }

    public function destroy(Testimonial $testimonial)
    {
        $this->testimonialService->delete($testimonial);

        return response()->json(['message' => 'Deleted']);
    }
}
