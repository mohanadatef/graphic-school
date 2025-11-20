<?php

namespace Modules\CMS\Contacts\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CMS\Contacts\Http\Resources\ContactMessageResource;
use Modules\CMS\Contacts\Models\ContactMessage;
use Modules\CMS\Contacts\Services\ContactMessageService;

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

