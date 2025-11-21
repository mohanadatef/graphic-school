<?php

namespace Modules\LMS\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\LMS\Attendance\Http\Requests\ListAttendanceRequest;
use Modules\LMS\Attendance\Http\Resources\AttendanceResource;
use Modules\LMS\Attendance\Services\AttendanceService;

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

