<?php

namespace Modules\ACL\Users\Repositories\Eloquent;

use Modules\ACL\Users\Models\User;
use Modules\ACL\Users\Repositories\Interfaces\UserRepositoryInterface;
use App\Support\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Make model instance
     */
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new User();
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

