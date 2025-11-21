<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

interface RoleRepositoryInterface
{
    public function find(int $id): ?Model;
    public function findByName(string $name): ?Model;
    public function all(): \Illuminate\Support\Collection;
}

