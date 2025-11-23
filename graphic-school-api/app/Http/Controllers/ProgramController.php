<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Repositories\Interfaces\ProgramRepositoryInterface;
use App\Repositories\Interfaces\BatchRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    public function __construct(
        private ProgramRepositoryInterface $programRepository,
        private BatchRepositoryInterface $batchRepository
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $locale = $request->header('Accept-Language') 
            ?? $request->query('locale') 
            ?? app()->getLocale();
        
        app()->setLocale($locale);
        
        $filters = [
            'is_active' => true,
            'type' => $request->query('type'),
        ];
        
        $programs = $this->programRepository->paginateWithFilters($filters, $request->integer('per_page', 12));
        
        $programs->getCollection()->transform(function ($program) use ($locale) {
            $programData = $program->toArray();
            $programData['title'] = $program->getTranslated('title', $locale);
            $programData['description'] = $program->getTranslated('description', $locale);
            $programData['meta_title'] = $program->getTranslated('meta_title', $locale);
            $programData['meta_description'] = $program->getTranslated('meta_description', $locale);
            return $programData;
        });
        
        return response()->json([
            'success' => true,
            'data' => $programs->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $programs->currentPage(),
                    'per_page' => $programs->perPage(),
                    'total' => $programs->total(),
                    'last_page' => $programs->lastPage(),
                ],
            ],
        ]);
    }

    public function show(string $slug, Request $request): JsonResponse
    {
        $locale = $request->header('Accept-Language') 
            ?? $request->query('locale') 
            ?? app()->getLocale();
        
        app()->setLocale($locale);
        
        $program = $this->programRepository->findBySlug($slug);
        
        if (!$program || !$program->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Program not found',
            ], 404);
        }
        
        $program = $this->programRepository->loadRelations($program, ['translations', 'batches.groups']);
        
        $programData = $program->toArray();
        $programData['title'] = $program->getTranslated('title', $locale);
        $programData['description'] = $program->getTranslated('description', $locale);
        $programData['meta_title'] = $program->getTranslated('meta_title', $locale);
        $programData['meta_description'] = $program->getTranslated('meta_description', $locale);
        
        // Translate batches and groups
        if (isset($programData['batches']) && $program->batches) {
            foreach ($programData['batches'] as &$batch) {
                $batchModel = $program->batches->firstWhere('id', $batch['id']);
                if ($batchModel) {
                    $batch['name'] = $batchModel->getTranslated('name', $locale);
                }
                if (isset($batch['groups']) && $batchModel) {
                    foreach ($batch['groups'] as &$group) {
                        $groupModel = $batchModel->groups->firstWhere('id', $group['id']);
                        if ($groupModel) {
                            $group['name'] = $groupModel->getTranslated('name', $locale);
                        }
                    }
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $programData,
        ]);
    }

    public function batches(string $slug, Request $request): JsonResponse
    {
        $locale = $request->header('Accept-Language') 
            ?? $request->query('locale') 
            ?? app()->getLocale();
        
        app()->setLocale($locale);
        
        $program = $this->programRepository->findBySlug($slug);
        
        if (!$program || !$program->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Program not found',
            ], 404);
        }
        
        $batches = $this->batchRepository->findByProgram($program->id);
        
        $batches->transform(function ($batch) use ($locale) {
            $batchData = $batch->toArray();
            $batchData['name'] = $batch->getTranslated('name', $locale);
            $batchData['description'] = $batch->getTranslated('description', $locale);
            return $batchData;
        });
        
        return response()->json([
            'success' => true,
            'data' => $batches,
        ]);
    }
}

