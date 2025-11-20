<?php

namespace Modules\CMS\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CMS\Settings\Http\Requests\UpdateSettingRequest;
use Modules\CMS\Settings\Services\SettingService;

class SettingController extends Controller
{
    protected array $allowedKeys = [
        'site_name',
        'email',
        'phone',
        'address',
        'about_us',
        'logo',
        'primary_color',
        'secondary_color',
        'facebook',
        'instagram',
    ];

    public function __construct(private SettingService $settingService)
    {
    }

    public function index()
    {
        return response()->json(
            $this->settingService->getSettings($this->allowedKeys)
        );
    }

    public function update(UpdateSettingRequest $request)
    {
        $data = $request->only($this->allowedKeys);

        $this->settingService->update($data, $request->file('logo_image'));

        return response()->json(['message' => 'Settings updated']);
    }
}

