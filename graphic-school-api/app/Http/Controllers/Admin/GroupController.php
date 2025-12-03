<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Services\EntityTranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GroupController extends Controller
{
    public function __construct(
        private GroupRepositoryInterface $groupRepository,
        private EntityTranslationService $translationService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['course_id', 'instructor_id', 'is_active', 'search']);
        $perPage = $request->integer('per_page', 15);
        
        $groups = $this->groupRepository->paginateWithFilters($filters, $perPage);
        
        $includeTranslations = $request->has('include_translations');
        $groups->getCollection()->transform(function ($group) use ($includeTranslations) {
            $locale = app()->getLocale();
            $groupData = $group->toArray();
            $groupData['name'] = $group->getTranslated('name', $locale);
            $groupData['description'] = $group->getTranslated('description', $locale);
            
            if ($includeTranslations) {
                $groupData['translations'] = $group->translations->map(fn($t) => $t->toArray());
            }
            
            return $groupData;
        });
        
        return response()->json([
            'success' => true,
            'data' => $groups->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $groups->currentPage(),
                    'per_page' => $groups->perPage(),
                    'total' => $groups->total(),
                    'last_page' => $groups->lastPage(),
                ],
            ],
        ]);
    }

    public function show(Group $group, Request $request): JsonResponse
    {
        $group = $this->groupRepository->loadRelations($group, ['translations', 'course', 'instructor', 'students', 'instructors', 'groupSessions']);
        
        $locale = app()->getLocale();
        $groupData = $group->toArray();
        $groupData['name'] = $group->getTranslated('name', $locale);
        $groupData['description'] = $group->getTranslated('description', $locale);
        
        if ($request->has('include_translations')) {
            $groupData['translations'] = $group->translations->map(fn($t) => $t->toArray());
        }
        
        return response()->json([
            'success' => true,
            'data' => $groupData,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'code' => 'required|string|max:50',
            'name' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'room' => 'nullable|string|max:255',
            'instructor_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
            'instructor_ids' => 'nullable|array',
            'instructor_ids.*' => 'exists:users,id',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required|string|in:en,ar',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);
        
        $translations = $validated['translations'];
        $studentIds = $validated['student_ids'] ?? [];
        $instructorIds = $validated['instructor_ids'] ?? [];
        unset($validated['translations'], $validated['student_ids'], $validated['instructor_ids']);
        
        $group = $this->groupRepository->create($validated);
        $this->translationService->saveTranslations($group, $translations);
        
        if (!empty($studentIds)) {
            $this->groupRepository->syncStudents($group, $studentIds);
        }
        
        if (!empty($instructorIds)) {
            $this->groupRepository->syncInstructors($group, $instructorIds);
        }
        
        $group->load(['translations', 'students', 'instructors']);
        
        return response()->json([
            'success' => true,
            'message' => 'Group created successfully',
            'data' => $group,
        ], 201);
    }

    public function update(Request $request, Group $group): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:1',
            'room' => 'nullable|string|max:255',
            'instructor_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
            'instructor_ids' => 'nullable|array',
            'instructor_ids.*' => 'exists:users,id',
            'translations' => 'sometimes|array',
            'translations.*.locale' => 'required|string|in:en,ar',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);
        
        $translations = $validated['translations'] ?? null;
        $studentIds = $validated['student_ids'] ?? null;
        $instructorIds = $validated['instructor_ids'] ?? null;
        
        if (isset($validated['translations'])) {
            unset($validated['translations']);
        }
        if (isset($validated['student_ids'])) {
            unset($validated['student_ids']);
        }
        if (isset($validated['instructor_ids'])) {
            unset($validated['instructor_ids']);
        }
        
        $group = $this->groupRepository->update($group, $validated);
        
        if ($translations) {
            $this->translationService->saveTranslations($group, $translations);
        }
        
        if ($studentIds !== null) {
            $this->groupRepository->syncStudents($group, $studentIds);
        }
        
        if ($instructorIds !== null) {
            $this->groupRepository->syncInstructors($group, $instructorIds);
        }
        
        $group->load(['translations', 'students', 'instructors']);
        
        return response()->json([
            'success' => true,
            'message' => 'Group updated successfully',
            'data' => $group,
        ]);
    }

    public function destroy(Group $group): JsonResponse
    {
        $group->translations()->delete();
        $this->groupRepository->delete($group);
        
        return response()->json([
            'success' => true,
            'message' => 'Group deleted successfully',
        ]);
    }
}

