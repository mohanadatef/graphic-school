<?php

namespace Modules\LMS\Enrollments\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Modules\LMS\Enrollments\Http\Requests\ListEnrollmentRequest;
use Modules\LMS\Enrollments\Http\Requests\StoreEnrollmentRequest;
use Modules\LMS\Enrollments\Http\Requests\UpdateEnrollmentRequest;
use Modules\LMS\Enrollments\Http\Resources\EnrollmentResource;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Enrollments\Services\EnrollmentService;

class EnrollmentController extends Controller
{
    public function __construct(private EnrollmentService $enrollmentService)
    {
    }

    public function index(ListEnrollmentRequest $request)
    {
        // Ensure per_page is within allowed range (max 100)
        $perPage = min(100, max(5, $request->integer('per_page', 10)));
        
        $enrollments = $this->enrollmentService->paginate(
            $request->validated(),
            $perPage
        );

        return ApiResponse::paginated(
            EnrollmentResource::collection($enrollments),
            'Enrollments retrieved successfully'
        );
    }

    public function store(StoreEnrollmentRequest $request)
    {
        $enrollment = $this->enrollmentService->create($request->validated());

        return ApiResponse::created(
            EnrollmentResource::make($enrollment->load(['student', 'course']))->resolve(request()),
            'Enrollment created successfully'
        );
    }

    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment)
    {
        $enrollment = $this->enrollmentService->update(
            $enrollment,
            $request->validated(),
            auth('api')->id()
        );

        return ApiResponse::success(
            EnrollmentResource::make($enrollment->load(['student', 'course']))->resolve(request()),
            'Enrollment updated successfully'
        );
    }
}

