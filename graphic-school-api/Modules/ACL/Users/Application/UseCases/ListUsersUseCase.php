<?php

namespace Modules\ACL\Users\Application\UseCases;

use App\Support\BaseQuery;
use App\Support\Table\TableBuilder;
use Modules\ACL\Users\Application\DTOs\ListUsersDTO;
use Modules\ACL\Users\Infrastructure\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListUsersUseCase extends BaseQuery
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    protected function handle(mixed $input): LengthAwarePaginator
    {
        /** @var ListUsersDTO $dto */
        $dto = $input;

        $builder = new TableBuilder($this->userRepository->query());
        $builder->sortable(['id', 'name', 'email', 'created_at', 'is_active']);
        $builder->searchable(['name', 'email', 'phone']);
        $builder->filterable(['role_id', 'is_active']);

        $builder->applySorting($dto->sortBy, $dto->sortOrder);
        $builder->applySearch($dto->search);
        $builder->applyFilters($dto->filters);
        $builder->applyDateRange($dto->dateFrom, $dto->dateTo);

        return $builder->paginate($dto->perPage, $dto->page);
    }
}

