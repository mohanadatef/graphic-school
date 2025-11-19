<?php

namespace App\Repositories\Eloquent;

use App\Models\ContactMessage;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactMessageRepository extends BaseRepository implements ContactMessageRepositoryInterface
{
    public function __construct(ContactMessage $model)
    {
        parent::__construct($model);
    }

    public function paginateOrdered(int $perPage): LengthAwarePaginator
    {
        return $this->query()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function markResolved(ContactMessage $contactMessage): ContactMessage
    {
        $contactMessage->update(['is_resolved' => true]);

        return $contactMessage;
    }
}


