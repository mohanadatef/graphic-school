<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use App\Services\ContactMessageService;

class ContactController extends Controller
{
    public function __construct(private ContactMessageService $contactMessageService)
    {
    }

    public function index()
    {
        return ContactMessageResource::collection(
            $this->contactMessageService->paginate()
        );
    }

    public function resolve(ContactMessage $contactMessage)
    {
        $message = $this->contactMessageService->resolve($contactMessage);

        return ContactMessageResource::make($message);
    }
}
