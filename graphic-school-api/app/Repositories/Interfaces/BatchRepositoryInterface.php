<?php

namespace App\Repositories\Interfaces;

use App\Support\Repositories\BaseRepositoryInterface;
use App\Models\Batch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BatchRepositoryInterface extends BaseRepositoryInterface
{
    public function findByProgram(int $programId): \Illuminate\Support\Collection;
    
    public function paginateWithFilters(array $filters, int $perPage): LengthAwarePaginator;
    
    public function loadRelations(Batch $batch, array $relations = []): Batch;
}

