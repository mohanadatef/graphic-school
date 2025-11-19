<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function paginateWithRole(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->query()->with('role');

        if (! empty($filters['role_id'])) {
            $query->where('role_id', (int) $filters['role_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function loadRole(User $user): User
    {
        return $user->load('role');
    }

    public function findByEmailWithRole(string $email): ?User
    {
        return $this->query()->where('email', $email)->with('role')->first();
    }
}


