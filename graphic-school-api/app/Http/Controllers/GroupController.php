<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GroupController extends Controller
{
    public function sessions(int $id, Request $request): JsonResponse
    {
        $locale = $request->header('Accept-Language') 
            ?? $request->query('locale') 
            ?? app()->getLocale();
        
        app()->setLocale($locale);
        
        $group = Group::with(['sessions.translations'])->findOrFail($id);
        
        $sessions = $group->sessions->map(function ($session) use ($locale) {
            $sessionData = $session->toArray();
            $sessionData['title'] = $session->getTranslated('title', $locale);
            $sessionData['note'] = $session->getTranslated('note', $locale);
            return $sessionData;
        });
        
        return response()->json([
            'success' => true,
            'data' => $sessions,
        ]);
    }
}

