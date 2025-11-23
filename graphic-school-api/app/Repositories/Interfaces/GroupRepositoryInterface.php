<?php

namespace App\Repositories\Interfaces;

use App\Support\Repositories\BaseRepositoryInterface;
use App\Models\Group;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface GroupRepositoryInterface extends BaseRepositoryInterface
{
    public function findByBatch(int $batchId): \Illuminate\Support\Collection;
    
    public function paginateWithFilters(array $filters, int $perPage): LengthAwarePaginator;
    
    public function loadRelations(Group $group, array $relations = []): Group;
    
    public function syncStudents(Group $group, array $studentIds): void;
    
    public function syncInstructors(Group $group, array $instructorIds): void;
}

