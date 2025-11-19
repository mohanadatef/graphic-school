<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Enrollment\ListEnrollmentRequest;
use App\Http\Requests\Admin\Enrollment\StoreEnrollmentRequest;
use App\Http\Requests\Admin\Enrollment\UpdateEnrollmentRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;
use App\Services\EnrollmentService;

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
