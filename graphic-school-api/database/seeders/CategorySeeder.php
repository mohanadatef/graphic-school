<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Branding & Identity',
            'Illustration',
            'UI / UX',
            'Typography',
            'Web Design',
            'Motion Graphics',
            'Packaging Design',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['name' => $name],
                ['name' => $name, 'is_active' => true]
            );
        }
    }
}
