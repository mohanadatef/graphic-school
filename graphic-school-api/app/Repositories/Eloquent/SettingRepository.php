<?php

namespace App\Repositories\Eloquent;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    public function getManyByKeys(array $keys): array
    {
        return $this->query()->whereIn('key', $keys)->pluck('value', 'key')->toArray();
    }

    public function getValue(string $key): ?string
    {
        return $this->query()->where('key', $key)->value('value');
    }

    public function updateOrCreate(array $attributes, array $values): void
    {
        $this->model->newQuery()->updateOrCreate($attributes, $values);
    }
}


