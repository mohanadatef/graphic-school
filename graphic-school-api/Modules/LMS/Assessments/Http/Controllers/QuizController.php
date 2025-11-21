<?php

namespace Modules\LMS\Assessments\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Modules\LMS\Assessments\Services\QuizService;
use Modules\LMS\Assessments\Http\Requests\StoreQuizRequest;
use Modules\LMS\Assessments\Http\Requests\UpdateQuizRequest;
use Modules\LMS\Assessments\Http\Requests\SubmitQuizRequest;
use Modules\LMS\Assessments\Http\Resources\QuizResource;
use Modules\LMS\Assessments\Http\Resources\QuizAttemptResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(private QuizService $quizService)
    {
    }

    public function store(StoreQuizRequest $request): JsonResponse
    {
        $quiz = $this->quizService->createQuiz($request->validated());
        return ApiResponse::success(new QuizResource($quiz), 'Quiz created successfully', 201);
    }

    public function update(int $quizId, UpdateQuizRequest $request): JsonResponse
    {
        $quiz = $this->quizService->updateQuiz($quizId, $request->validated());
        return ApiResponse::success(new QuizResource($quiz), 'Quiz updated successfully');
    }

    public function submit(SubmitQuizRequest $request, int $quizId): JsonResponse
    {
        $studentId = $request->user()->id;
        $attempt = $this->quizService->submitQuiz($studentId, $quizId, $request->input('answers', []));
        return ApiResponse::success(new QuizAttemptResource($attempt->load('quiz')), 'Quiz submitted successfully');
    }

    public function getAttempts(Request $request, int $quizId): JsonResponse
    {
        $studentId = $request->user()->id;
        $attempts = $this->quizService->getStudentAttempts($studentId, $quizId);
        return ApiResponse::success($attempts, 'Quiz attempts retrieved successfully');
    }

    public function show(int $quizId): JsonResponse
    {
        $quiz = $this->quizService->getQuiz($quizId);
        return ApiResponse::success(new QuizResource($quiz), 'Quiz retrieved successfully');
    }

    public function getStudentQuizzes(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;
        $quizzes = $this->quizService->getStudentQuizzes($studentId);
        return ApiResponse::success($quizzes, 'Student quizzes retrieved successfully');
    }
}

