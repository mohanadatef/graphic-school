<?php

namespace Modules\LMS\Sessions\Services;

use Modules\LMS\Sessions\Models\Session;
use Modules\LMS\Sessions\Repositories\Interfaces\SessionRepositoryInterface;
use Modules\ACL\Users\Models\User;
use App\Services\EntityTranslationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SessionService
{
    public function __construct(private SessionRepositoryInterface $sessionRepository)
    {
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->sessionRepository->paginateForAdmin($filters, $perPage);
    }

    public function show(Session $session): Session
    {
        return $this->sessionRepository->loadWithCourse($session);
    }

    public function update(Session $session, array $data): Session
    {
        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $session = $this->sessionRepository->update($session, $data);

        // Save translations if provided
        if (!empty($translations)) {
            $translationService = app(EntityTranslationService::class);
            $translationService->saveTranslations($session, $translations);
        }

        return $this->sessionRepository->loadWithCourse($session);
    }

    public function delete(Session $session): void
    {
        // Delete translations
        $translationService = app(EntityTranslationService::class);
        $translationService->deleteTranslations($session);

        $this->sessionRepository->delete($session);
    }

    /**
     * Update student comment and/or file for a session
     */
    public function updateStudentContent(Session $session, User $student, array $data, ?UploadedFile $file = null): Session
    {
        $updateData = [];

        if (isset($data['student_comment'])) {
            $updateData['student_comment'] = $data['student_comment'];
        }

        if ($file) {
            // Delete old file if exists
            if ($session->student_file_path) {
                Storage::disk('public')->delete($session->student_file_path);
            }

            $updateData['student_file_path'] = $file->store('sessions/student-files', 'public');
        }

        if (!empty($updateData)) {
            $session = $this->sessionRepository->update($session, $updateData);
        }

        return $this->sessionRepository->loadWithCourse($session);
    }

    /**
     * Update instructor comment for a session
     */
    public function updateInstructorComment(Session $session, User $instructor, string $comment): Session
    {
        $updateData = ['instructor_comment' => $comment];
        $session = $this->sessionRepository->update($session, $updateData);

        return $this->sessionRepository->loadWithCourse($session);
    }

    /**
     * Update supervisor comment for a session
     */
    public function updateSupervisorComment(Session $session, User $supervisor, string $comment): Session
    {
        $updateData = ['supervisor_comment' => $comment];
        $session = $this->sessionRepository->update($session, $updateData);

        return $this->sessionRepository->loadWithCourse($session);
    }

    /**
     * Get next session for a course based on current date
     */
    public function getNextSessionForCourse(int $courseId): ?Session
    {
        return $this->sessionRepository->query()
            ->where('course_id', $courseId)
            ->where('session_date', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->first();
    }
}

