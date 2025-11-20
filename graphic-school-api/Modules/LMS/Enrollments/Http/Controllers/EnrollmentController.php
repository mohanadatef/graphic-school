<?php

namespace Modules\LMS\Enrollments\Http\Controllers;

use App\Http\Controllers\Controller;
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
        $enrollments = $this->enrollmentService->paginate(
            $request->validated(),
            $request->integer('per_page', 10)
        );

        return EnrollmentResource::collection($enrollments);
    }

    public function store(StoreEnrollmentRequest $request)
    {
        $enrollment = $this->enrollmentService->create($request->validated());

        return EnrollmentResource::make($enrollment)
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment)
    {
        $enrollment = $this->enrollmentService->update(
            $enrollment,
            $request->validated(),
            $request->user()->id
        );

        return EnrollmentResource::make($enrollment);
    }
}

