<?php

namespace Modules\CMS\Settings\Repositories\Interfaces;

use App\Support\Repositories\BaseRepositoryInterface;

interface SettingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param  array<int, string>  $keys
     * @return array<string, mixed>
     */
    public function getManyByKeys(array $keys): array;

    public function getValue(string $key): ?string;

    public function updateOrCreate(array $attributes, array $values): void;
}

