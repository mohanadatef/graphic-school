<?php

namespace Modules\LMS\Courses\Application\UseCases;

use App\Support\BaseQuery;
use App\Support\Table\TableBuilder;
use Modules\LMS\Courses\Application\DTOs\ListCoursesDTO;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListCoursesUseCase extends BaseQuery
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {
    }

    protected function handle(mixed $input): LengthAwarePaginator
    {
        /** @var ListCoursesDTO $dto */
        $dto = $input;

        $builder = new TableBuilder($this->courseRepository->query());
        $builder->sortable(['id', 'title', 'code', 'price', 'start_date', 'created_at', 'status']);
        $builder->searchable(['title', 'code', 'description']);
        $builder->filterable(['category_id', 'status', 'is_published', 'delivery_type']);

        $builder->applySorting($dto->sortBy, $dto->sortOrder);
        $builder->applySearch($dto->search);
        $builder->applyFilters($dto->filters);
        $builder->applyDateRange($dto->dateFrom, $dto->dateTo);

        return $builder->paginate($dto->perPage, $dto->page);
    }
}

