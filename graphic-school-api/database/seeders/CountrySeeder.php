<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Add Egypt as default
     */
    public function run(): void
    {
        $countries = [
            [
                'code' => 'EG',
                'name' => 'Egypt',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
        ];

        // If there's already a default country, unset it before setting new default
        foreach ($countries as $countryData) {
            if ($countryData['is_default']) {
                Country::where('is_default', true)->update(['is_default' => false]);
            }
        }

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                $country
            );
        }

        $this->command->info('✓ Countries seeded successfully!');
    }
}

