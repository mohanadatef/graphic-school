<?php

namespace Modules\ACL\Users\Infrastructure\Repositories\Eloquent;

use Modules\ACL\Users\Models\User;
use Modules\ACL\Users\Infrastructure\Repositories\Interfaces\UserRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface as SharedUserRepositoryInterface;
use App\Support\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class UserRepository extends BaseRepository implements UserRepositoryInterface, SharedUserRepositoryInterface
{
    protected function makeModel(): User
    {
        return new User();
    }

    public function paginateWithRole(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->query()->with('role');

        if (!empty($filters['role_id'])) {
            $query->where('role_id', (int) $filters['role_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * Load role relationship
     * Implements both Module interface (User-specific) and Shared interface (Model-based)
     * PHP doesn't support method overloading, so we use a single method that accepts both types
     */
    public function loadRole(User|Model $user): User|Model
    {
        if (!($user instanceof User)) {
            throw new \InvalidArgumentException('Expected User model');
        }
        return $user->load('role');
    }

    public function findByEmailWithRole(string $email): ?User
    {
        return $this->query()->where('email', $email)->with('role')->first();
    }

    public function findByEmail(string $email): ?Model
    {
        return $this->query()->where('email', $email)->first();
    }
}

