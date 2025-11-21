<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

interface SessionRepositoryInterface
{
    public function create(array $data): Model;
    public function deleteByCourse(int $courseId): void;
    public function query(): \Illuminate\Database\Eloquent\Builder;
}

