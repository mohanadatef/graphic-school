<?php

namespace Modules\ACL\Users\Application\DTOs;

use App\Support\DTOs\BaseDTO;

class ListUsersDTO extends BaseDTO
{
    public int $page = 1;
    public int $perPage = 15;
    public ?string $sortBy = null;
    public ?string $sortOrder = null;
    public ?string $search = null;
    public array $filters = [];
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
}

