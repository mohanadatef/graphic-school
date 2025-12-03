<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Add EGP (default) and USD (optional)
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'EGP',
                'name' => 'Egyptian Pound',
                'symbol' => 'EGP',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
        ];

        // If there's already a default currency, unset it before setting new default
        foreach ($currencies as $currencyData) {
            if ($currencyData['is_default']) {
                Currency::where('is_default', true)->update(['is_default' => false]);
            }
        }

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }

        $this->command->info('✓ Currencies seeded successfully!');
    }
}

