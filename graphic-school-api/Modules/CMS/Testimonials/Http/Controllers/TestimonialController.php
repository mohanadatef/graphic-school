<?php

namespace Modules\CMS\Testimonials\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CMS\Testimonials\Http\Requests\UpdateTestimonialRequest;
use Modules\CMS\Testimonials\Http\Resources\TestimonialResource;
use Modules\CMS\Testimonials\Models\Testimonial;
use Modules\CMS\Testimonials\Services\TestimonialService;

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

