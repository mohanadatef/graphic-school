<?php

namespace Modules\CMS\Contacts\Repositories\Eloquent;

use App\Support\Repositories\BaseRepository;
use Modules\CMS\Contacts\Models\ContactMessage;
use Modules\CMS\Contacts\Repositories\Interfaces\ContactMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactMessageRepository extends BaseRepository implements ContactMessageRepositoryInterface
{
    /**
     * Make model instance
     */
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new ContactMessage();
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

