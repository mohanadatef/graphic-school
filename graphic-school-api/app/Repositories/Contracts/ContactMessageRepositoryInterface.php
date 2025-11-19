<?php

namespace App\Repositories\Contracts;

use App\Models\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ContactMessageRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateOrdered(int $perPage): LengthAwarePaginator;

    public function markResolved(ContactMessage $contactMessage): ContactMessage;
}


