<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactMessageService
{
    public function __construct(private ContactMessageRepositoryInterface $contactMessageRepository)
    {
    }

    public function paginate(int $perPage = 30): LengthAwarePaginator
    {
        return $this->contactMessageRepository->paginateOrdered($perPage);
    }

    public function resolve(ContactMessage $contactMessage): ContactMessage
    {
        return $this->contactMessageRepository->markResolved($contactMessage);
    }
}

