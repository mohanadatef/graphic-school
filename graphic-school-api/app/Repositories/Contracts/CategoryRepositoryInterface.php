<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function listOrdered(): Collection;

    public function activeOrdered(): Collection;
}


