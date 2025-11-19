<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Attendance\ListAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Services\AttendanceService;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $attendanceService)
    {
    }

    public function index(ListAttendanceRequest $request)
    {
        $attendances = $this->attendanceService->paginate(
            $request->validated(),
            $request->integer('per_page', 50)
        );

        return AttendanceResource::collection($attendances);
    }
}
