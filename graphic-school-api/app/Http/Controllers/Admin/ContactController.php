<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function index()
    {
        return response()->json(
            ContactMessage::orderByDesc('created_at')->paginate(30)
        );
    }

    public function resolve(ContactMessage $contactMessage)
    {
        $contactMessage->update(['is_resolved' => true]);

        return response()->json($contactMessage);
    }
}
