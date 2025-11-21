<?php

namespace Modules\CMS\Contacts\Repositories\Interfaces;

use App\Support\Repositories\BaseRepositoryInterface;
use Modules\CMS\Contacts\Models\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ContactMessageRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateOrdered(int $perPage): LengthAwarePaginator;

    public function markResolved(ContactMessage $contactMessage): ContactMessage;
}

