<?php

namespace Modules\LMS\Categories\Repositories\Interfaces;

use App\Support\Repositories\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function listOrdered(): Collection;

    public function activeOrdered(): Collection;
}

