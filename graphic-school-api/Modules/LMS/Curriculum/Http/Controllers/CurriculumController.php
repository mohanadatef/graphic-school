<?php

namespace Modules\LMS\Curriculum\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Modules\LMS\Curriculum\Services\CurriculumService;
use Modules\LMS\Curriculum\Http\Requests\StoreModuleRequest;
use Modules\LMS\Curriculum\Http\Requests\UpdateModuleRequest;
use Modules\LMS\Curriculum\Http\Requests\StoreLessonRequest;
use Modules\LMS\Curriculum\Http\Requests\UpdateLessonRequest;
use Modules\LMS\Curriculum\Http\Requests\StoreResourceRequest;
use Modules\LMS\Curriculum\Http\Resources\CourseModuleResource;
use Modules\LMS\Curriculum\Http\Resources\LessonResource;
use Modules\LMS\Curriculum\Http\Resources\LessonResourceResource;
use Illuminate\Http\JsonResponse;

class CurriculumController extends Controller
{
    public function __construct(private CurriculumService $curriculumService)
    {
    }

    public function getCourseCurriculum(int $courseId): JsonResponse
    {
        $curriculum = $this->curriculumService->getCourseCurriculum($courseId);
        return ApiResponse::success([
            'course' => [
                'id' => $curriculum->id,
                'title' => $curriculum->title,
                'description' => $curriculum->description,
                'total_lessons' => $curriculum->total_lessons,
                'total_modules' => $curriculum->total_modules,
                'estimated_duration' => $curriculum->estimated_duration,
            ],
            'modules' => CourseModuleResource::collection($curriculum->modules),
        ], 'Course curriculum retrieved successfully');
    }

    public function storeModule(StoreModuleRequest $request): JsonResponse
    {
        $module = $this->curriculumService->createModule($request->validated());
        return ApiResponse::success(new CourseModuleResource($module), 'Module created successfully', 201);
    }

    public function updateModule(int $moduleId, UpdateModuleRequest $request): JsonResponse
    {
        $module = $this->curriculumService->updateModule($moduleId, $request->validated());
        return ApiResponse::success(new CourseModuleResource($module), 'Module updated successfully');
    }

    public function deleteModule(int $moduleId): JsonResponse
    {
        $this->curriculumService->deleteModule($moduleId);
        return ApiResponse::success(null, 'Module deleted successfully');
    }

    public function storeLesson(StoreLessonRequest $request): JsonResponse
    {
        $lesson = $this->curriculumService->createLesson($request->validated());
        return ApiResponse::success(new LessonResource($lesson), 'Lesson created successfully', 201);
    }

    public function updateLesson(int $lessonId, UpdateLessonRequest $request): JsonResponse
    {
        $lesson = $this->curriculumService->updateLesson($lessonId, $request->validated());
        return ApiResponse::success(new LessonResource($lesson), 'Lesson updated successfully');
    }

    public function deleteLesson(int $lessonId): JsonResponse
    {
        $this->curriculumService->deleteLesson($lessonId);
        return ApiResponse::success(null, 'Lesson deleted successfully');
    }

    public function storeResource(StoreResourceRequest $request): JsonResponse
    {
        $resource = $this->curriculumService->addResource($request->validated());
        return ApiResponse::success(new LessonResourceResource($resource), 'Resource added successfully', 201);
    }

    public function updateResource(int $resourceId, StoreResourceRequest $request): JsonResponse
    {
        $resource = $this->curriculumService->updateResource($resourceId, $request->validated());
        return ApiResponse::success(new LessonResourceResource($resource), 'Resource updated successfully');
    }

    public function deleteResource(int $resourceId): JsonResponse
    {
        $this->curriculumService->deleteResource($resourceId);
        return ApiResponse::success(null, 'Resource deleted successfully');
    }
}

