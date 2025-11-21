<?php

namespace Modules\LMS\Assessments\Services;

use Modules\LMS\Assessments\Models\Quiz;
use Modules\LMS\Assessments\Models\QuizQuestion;
use Modules\LMS\Assessments\Models\QuizAttempt;
use Modules\LMS\Enrollments\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function createQuiz(array $data): Quiz
    {
        return DB::transaction(function () use ($data) {
            $quiz = Quiz::create($data);
            
            if (isset($data['questions']) && is_array($data['questions'])) {
                foreach ($data['questions'] as $questionData) {
                    $quiz->questions()->create($questionData);
                }
            }
            
            return $quiz->load('questions');
        });
    }

    public function updateQuiz(int $quizId, array $data): Quiz
    {
        return DB::transaction(function () use ($quizId, $data) {
            $quiz = Quiz::findOrFail($quizId);
            $quiz->update($data);
            
            if (isset($data['questions']) && is_array($data['questions'])) {
                // Delete existing questions
                $quiz->questions()->delete();
                
                // Create new questions
                foreach ($data['questions'] as $questionData) {
                    $quiz->questions()->create($questionData);
                }
            }
            
            return $quiz->fresh('questions');
        });
    }

    public function submitQuiz(int $studentId, int $quizId, array $answers): QuizAttempt
    {
        return DB::transaction(function () use ($studentId, $quizId, $answers) {
            $quiz = Quiz::with('questions')->findOrFail($quizId);
            $enrollment = Enrollment::where('student_id', $studentId)
                ->where('course_id', $quiz->course_id)
                ->where('status', 'approved')
                ->firstOrFail();

            // Check max attempts
            $attemptsCount = QuizAttempt::where('student_id', $studentId)
                ->where('quiz_id', $quizId)
                ->where('enrollment_id', $enrollment->id)
                ->count();

            if ($attemptsCount >= $quiz->max_attempts) {
                throw new \Exception('تم الوصول للحد الأقصى من المحاولات');
            }

            // Calculate score
            $score = 0;
            $totalPoints = 0;
            $correctAnswers = [];

            foreach ($quiz->questions as $question) {
                $totalPoints += $question->points;
                $studentAnswer = $answers[$question->id] ?? null;
                $isCorrect = $this->checkAnswer($question, $studentAnswer);
                
                if ($isCorrect) {
                    $score += $question->points;
                }
                
                $correctAnswers[$question->id] = [
                    'student_answer' => $studentAnswer,
                    'correct_answer' => $question->correct_answers,
                    'is_correct' => $isCorrect,
                    'points' => $isCorrect ? $question->points : 0,
                ];
            }

            $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 2) : 0;
            $isPassed = $percentage >= $quiz->passing_score;

            $attempt = QuizAttempt::create([
                'student_id' => $studentId,
                'quiz_id' => $quizId,
                'enrollment_id' => $enrollment->id,
                'answers' => $correctAnswers,
                'score' => $score,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
                'is_passed' => $isPassed,
                'started_at' => now(),
                'completed_at' => now(),
            ]);

            return $attempt;
        });
    }

    protected function checkAnswer(QuizQuestion $question, $studentAnswer): bool
    {
        $correctAnswers = $question->correct_answers ?? [];
        
        if (empty($correctAnswers)) {
            return false;
        }

        switch ($question->type) {
            case 'multiple_choice':
            case 'true_false':
                return in_array($studentAnswer, $correctAnswers);
            
            case 'short_answer':
                $studentAnswer = strtolower(trim($studentAnswer));
                return in_array(strtolower(trim($studentAnswer)), array_map('strtolower', $correctAnswers));
            
            case 'essay':
                // Essays need manual grading
                return false;
            
            default:
                return false;
        }
    }

    public function getStudentAttempts(int $studentId, int $quizId): array
    {
        $quiz = Quiz::findOrFail($quizId);
        $enrollment = Enrollment::where('student_id', $studentId)
            ->where('course_id', $quiz->course_id)
            ->where('status', 'approved')
            ->firstOrFail();

        $attempts = QuizAttempt::where('student_id', $studentId)
            ->where('quiz_id', $quizId)
            ->where('enrollment_id', $enrollment->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'quiz' => $quiz,
            'attempts' => $attempts,
            'attempts_count' => $attempts->count(),
            'max_attempts' => $quiz->max_attempts,
            'can_retake' => $attempts->count() < $quiz->max_attempts,
            'best_score' => $attempts->max('percentage') ?? 0,
            'passed' => $attempts->where('is_passed', true)->isNotEmpty(),
        ];
    }

    public function getQuiz(int $quizId): Quiz
    {
        return Quiz::with('questions')->findOrFail($quizId);
    }

    public function getStudentQuizzes(int $studentId): array
    {
        // Get all enrolled courses
        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->pluck('course_id');

        // Get all quizzes for these courses
        $quizzes = Quiz::whereIn('course_id', $enrollments)
            ->where('is_published', true)
            ->with(['course', 'module', 'lesson'])
            ->get();

        // Load attempts for each quiz
        $quizzesWithAttempts = $quizzes->map(function ($quiz) use ($studentId) {
            $enrollment = Enrollment::where('student_id', $studentId)
                ->where('course_id', $quiz->course_id)
                ->where('status', 'approved')
                ->first();

            if (!$enrollment) {
                return null;
            }

            $attempts = QuizAttempt::where('student_id', $studentId)
                ->where('quiz_id', $quiz->id)
                ->where('enrollment_id', $enrollment->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'course_id' => $quiz->course_id,
                'course' => $quiz->course,
                'time_limit' => $quiz->time_limit,
                'passing_score' => $quiz->passing_score,
                'max_attempts' => $quiz->max_attempts,
                'attempts' => $attempts,
                'attempts_count' => $attempts->count(),
                'can_retake' => $attempts->count() < $quiz->max_attempts,
                'best_score' => $attempts->max('percentage') ?? 0,
                'passed' => $attempts->where('is_passed', true)->isNotEmpty(),
            ];
        })->filter();

        return $quizzesWithAttempts->values()->all();
    }
}

