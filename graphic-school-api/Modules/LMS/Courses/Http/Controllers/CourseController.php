<?php

namespace Modules\LMS\Courses\Http\Controllers;

use App\Support\Controllers\BaseController;
use Modules\LMS\Courses\Http\Requests\AssignCourseInstructorsRequest;
use Modules\LMS\Courses\Http\Requests\GenerateSessionsRequest;
use Modules\LMS\Courses\Http\Requests\ListCourseRequest;
use Modules\LMS\Courses\Http\Requests\StoreCourseRequest;
use Modules\LMS\Courses\Http\Requests\UpdateCourseRequest;
use Modules\LMS\Courses\Http\Resources\CourseResource;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Courses\Application\UseCases\CreateCourseUseCase;
use Modules\LMS\Courses\Application\UseCases\UpdateCourseUseCase;
use Modules\LMS\Courses\Application\UseCases\DeleteCourseUseCase;
use Modules\LMS\Courses\Application\UseCases\ListCoursesUseCase;
use Modules\LMS\Courses\Application\UseCases\ShowCourseUseCase;
use Modules\LMS\Courses\Application\UseCases\AssignInstructorsUseCase;
use Modules\LMS\Courses\Application\UseCases\GenerateSessionsUseCase;
use Modules\LMS\Courses\Application\DTOs\CreateCourseDTO;
use Modules\LMS\Courses\Application\DTOs\UpdateCourseDTO;
use Modules\LMS\Courses\Application\DTOs\ListCoursesDTO;
use Modules\LMS\Courses\Application\DTOs\AssignInstructorsDTO;
use Modules\LMS\Courses\Application\DTOs\GenerateSessionsDTO;

class CourseController extends BaseController
{
    public function index(ListCourseRequest $request, ListCoursesUseCase $useCase)
    {
        $dto = ListCoursesDTO::fromArray([
            'page' => $request->integer('page', 1),
            'per_page' => $request->integer('per_page', 15),
            'sort_by' => $request->string('sort_by'),
            'sort_order' => $request->string('sort_order'),
            'search' => $request->string('search'),
            'filters' => $request->array('filters', []),
            'date_from' => $request->date('date_from'),
            'date_to' => $request->date('date_to'),
        ]);

        $paginator = $useCase->execute($dto);

        return $this->paginated(
            CourseResource::collection($paginator),
            'Courses retrieved successfully'
        );
    }

    public function store(StoreCourseRequest $request, CreateCourseUseCase $useCase)
    {
        $data = $request->validated();
        $data['image'] = $request->file('image');
        
        $dto = CreateCourseDTO::fromArray($data);
        $course = $useCase->execute($dto);

        return $this->created(
            CourseResource::make($course),
            'Course created successfully'
        );
    }

    public function show(Course $course, ShowCourseUseCase $useCase)
    {
        $course = $useCase->execute($course);

        return $this->success(
            CourseResource::make($course),
            'Course retrieved successfully'
        );
    }

    public function update(UpdateCourseRequest $request, Course $course, UpdateCourseUseCase $useCase)
    {
        $data = $request->validated();
        $data['image'] = $request->file('image');
        $data['regenerate_sessions'] = $request->boolean('regenerate_sessions', false);
        
        $dto = UpdateCourseDTO::fromArray($data);
        $course = $useCase->execute([$course, $dto]);

        return $this->success(
            CourseResource::make($course),
            'Course updated successfully'
        );
    }

    public function destroy(Course $course, DeleteCourseUseCase $useCase)
    {
        $useCase->execute($course);

        return $this->success(null, 'Course deleted successfully');
    }

    public function assignInstructors(AssignCourseInstructorsRequest $request, Course $course, AssignInstructorsUseCase $useCase)
    {
        $dto = AssignInstructorsDTO::fromArray($request->validated());
        $course = $useCase->execute([$course, $dto]);

        return $this->success(
            CourseResource::make($course),
            'Instructors assigned successfully'
        );
    }

    public function generateSessions(GenerateSessionsRequest $request, Course $course, GenerateSessionsUseCase $useCase)
    {
        $dto = GenerateSessionsDTO::fromArray($request->validated());
        $course = $useCase->execute([$course, $dto]);

        return $this->success(
            CourseResource::make($course),
            'Sessions generated successfully'
        );
    }
}
