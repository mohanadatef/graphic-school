<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->paginateWithRole($filters, $perPage);
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $data['is_active'] ?? true;

        $user = $this->userRepository->create($data);

        return $this->userRepository->loadRole($user);
    }

    public function update(User $user, array $data): User
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->userRepository->update($user, $data);

        return $this->userRepository->loadRole($user);
    }

    public function delete(User $user): void
    {
        if ($user->isAdmin()) {
            abort(422, 'لا يمكن حذف مدير النظام');
        }

        $this->userRepository->delete($user);
    }
}

