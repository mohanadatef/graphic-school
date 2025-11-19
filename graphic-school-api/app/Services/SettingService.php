<?php

namespace App\Services;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    public function __construct(private SettingRepositoryInterface $settingRepository)
    {
    }

    /**
     * @param  array<int, string>  $keys
     */
    public function getSettings(array $keys): array
    {
        $settings = $this->settingRepository->getManyByKeys($keys);

        if (! empty($settings['logo'])) {
            $settings['logo_url'] = Storage::disk('public')->url($settings['logo']);
        }

        return $settings;
    }

    public function update(array $data, ?UploadedFile $logo = null): void
    {
        if ($logo) {
            $oldLogo = $this->settingRepository->getValue('logo');

            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $data['logo'] = $logo->store('settings', 'public');
        }

        unset($data['logo_image']);

        foreach ($data as $key => $value) {
            $this->settingRepository->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
