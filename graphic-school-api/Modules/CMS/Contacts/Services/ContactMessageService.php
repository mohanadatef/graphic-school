<?php

namespace Modules\CMS\Contacts\Services;

use Modules\CMS\Contacts\Models\ContactMessage;
use Modules\CMS\Contacts\Repositories\Interfaces\ContactMessageRepositoryInterface;
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

