<?php

namespace App\Services;

use App\Models\Session;
use App\Repositories\Contracts\SessionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SessionService
{
    public function __construct(private SessionRepositoryInterface $sessionRepository)
    {
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->sessionRepository->paginateForAdmin($filters, $perPage);
    }

    public function show(Session $session): Session
    {
        return $this->sessionRepository->loadWithCourse($session);
    }

    public function update(Session $session, array $data): Session
    {
        $session = $this->sessionRepository->update($session, $data);

        return $this->sessionRepository->loadWithCourse($session);
    }

    public function delete(Session $session): void
    {
        $this->sessionRepository->delete($session);
    }
}

