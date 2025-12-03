<?php

namespace App\Services;

use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Sessions\Models\GroupSession;
use Modules\LMS\Attendance\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnrollmentService
{
    /**
     * Create enrollment for a course
     */
    public function createEnrollment(int $studentId, int $courseId, ?int $groupId = null): Enrollment
    {
        return DB::transaction(function () use ($studentId, $courseId, $groupId) {
            // Verify course exists
            $course = Course::findOrFail($courseId);
            
            // Verify group belongs to course if provided
            if ($groupId) {
                $group = \App\Models\Group::where('id', $groupId)
                    ->where('course_id', $courseId)
                    ->firstOrFail();
            }
            
            $enrollment = Enrollment::create([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'group_id' => $groupId,
                'status' => 'pending',
            ]);

            return $enrollment;
        });
    }

    /**
     * Approve enrollment and assign to group
     */
    public function approveEnrollment(int $enrollmentId, ?int $adminId = null, ?int $groupId = null): Enrollment
    {
        return DB::transaction(function () use ($enrollmentId, $adminId, $groupId) {
            $enrollment = Enrollment::findOrFail($enrollmentId);
            $enrollment->load(['course', 'student']);

            // Assign group if provided
            if ($groupId) {
                // Verify group belongs to the course
                $group = \App\Models\Group::where('id', $groupId)
                    ->where('course_id', $enrollment->course_id)
                    ->firstOrFail();
                
                // Check group capacity
                if (!$group->hasCapacity()) {
                    throw new \Exception('Group has reached maximum capacity');
                }
                
                $enrollment->group_id = $groupId;
            }

            $enrollment->status = 'approved';
            $enrollment->can_attend = true;
            $enrollment->approved_by = $adminId;
            $enrollment->approved_at = now();
            $enrollment->save();

            // Add student to group if group is assigned
            if ($enrollment->group_id) {
                $group = \App\Models\Group::find($enrollment->group_id);
                if ($group) {
                    $group->students()->syncWithoutDetaching([$enrollment->student_id => ['enrolled_at' => now()]]);
                    
                    // Create attendance slots for all existing sessions in the group
                    $this->createAttendanceSlots($enrollment);
                }
            }

            return $enrollment->load(['course', 'group', 'student']);
        });
    }

    /**
     * Reject enrollment
     */
    public function rejectEnrollment(int $enrollmentId, ?int $adminId = null, string $reason = ''): Enrollment
    {
        return DB::transaction(function () use ($enrollmentId, $adminId, $reason) {
            $enrollment = Enrollment::findOrFail($enrollmentId);
            $enrollment->status = 'rejected';
            if ($reason) {
                $enrollment->note = $reason;
            }
            $enrollment->save();

            return $enrollment->load(['course', 'student']);
        });
    }

    /**
     * Withdraw enrollment
     */
    public function withdrawEnrollment(int $enrollmentId, ?int $adminId = null): Enrollment
    {
        return DB::transaction(function () use ($enrollmentId, $adminId) {
            $enrollment = Enrollment::findOrFail($enrollmentId);
            $enrollment->status = 'withdrawn';
            $enrollment->can_attend = false;
            $enrollment->save();

            // Remove student from group if assigned
            if ($enrollment->group_id) {
                $group = \App\Models\Group::find($enrollment->group_id);
                if ($group) {
                    $group->students()->detach($enrollment->student_id);
                }
            }

            return $enrollment->load(['course', 'group', 'student']);
        });
    }

    /**
     * Create attendance slots for all sessions in the group
     */
    protected function createAttendanceSlots(Enrollment $enrollment): void
    {
        if (!$enrollment->group_id) {
            return;
        }

        $sessions = GroupSession::where('group_id', $enrollment->group_id)->get();

        foreach ($sessions as $session) {
            Attendance::firstOrCreate(
                [
                    'group_session_id' => $session->id,
                    'student_id' => $enrollment->student_id,
                ],
                [
                    'status' => 'absent',
                ]
            );
        }
    }
}
