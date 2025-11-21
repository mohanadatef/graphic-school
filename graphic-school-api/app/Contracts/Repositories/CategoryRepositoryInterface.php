<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function find(int $id): ?Model;
    public function all(): Collection;
}

