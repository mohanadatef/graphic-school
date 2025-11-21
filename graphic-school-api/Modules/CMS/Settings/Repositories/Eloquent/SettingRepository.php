<?php

namespace Modules\CMS\Settings\Repositories\Eloquent;

use App\Support\Repositories\BaseRepository;
use Modules\CMS\Settings\Models\Setting;
use Modules\CMS\Settings\Repositories\Interfaces\SettingRepositoryInterface;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    /**
     * Make model instance
     */
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Setting();
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

