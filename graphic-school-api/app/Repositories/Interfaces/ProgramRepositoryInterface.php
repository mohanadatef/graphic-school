<?php

namespace App\Repositories\Interfaces;

use App\Support\Repositories\BaseRepositoryInterface;
use App\Models\Program;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProgramRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?Program;
    
    public function paginateWithFilters(array $filters, int $perPage): LengthAwarePaginator;
    
    public function loadRelations(Program $program, array $relations = []): Program;
}

