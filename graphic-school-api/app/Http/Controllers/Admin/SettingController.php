<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function index()
    {
        $settings = Setting::whereIn('key', $this->allowedKeys)->pluck('value', 'key');

        if ($logo = $settings['logo'] ?? null) {
            $settings['logo_url'] = Storage::disk('public')->url($logo);
        }

        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'about_us' => ['nullable', 'string'],
            'primary_color' => ['nullable', 'string', 'max:10'],
            'secondary_color' => ['nullable', 'string', 'max:10'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'logo_image' => ['nullable', 'file', 'image', 'max:4096'],
        ]);

        $data = $request->only($this->allowedKeys);

        if ($request->hasFile('logo_image')) {
            $oldLogo = Setting::where('key', 'logo')->value('value');

            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $data['logo'] = $request->file('logo_image')->store('settings', 'public');
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json(['message' => 'Settings updated']);
    }
}
