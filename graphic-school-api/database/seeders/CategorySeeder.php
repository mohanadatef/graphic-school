<?php

namespace Database\Seeders;

use Modules\LMS\Categories\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['translations'=>["en"=>'Branding & Identity']],
            ['translations'=>["en"=>'Branding']],
            ['translations'=>["en"=>'Branding1']],
        ];

        foreach ($categories as $category) {
            $categor=  Category::create([ 'is_active' => true]
            );

            foreach ($category['translations'] as $locale => $name) {
                    if (!empty($name)) {
                        $categor->translations()->create([
                            'locale' => $locale,
                            'name' => $name,
                        ]);
                    }
                }
        }
    }
}
