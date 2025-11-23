<?php

namespace Database\Factories;

use App\Models\CertificateTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateTemplateFactory extends Factory
{
    protected $model = CertificateTemplate::class;

    public function definition(): array
    {
        return [
            'title' => 'Default Certificate Template',
            'background_image' => null,
            'layout' => [
                'student_name' => ['x' => 50, 'y' => 40, 'font_size' => 24],
                'program_name' => ['x' => 50, 'y' => 50, 'font_size' => 20],
                'issue_date' => ['x' => 50, 'y' => 70, 'font_size' => 14],
            ],
            'font_main' => 'Cairo',
            'font_headings' => 'Poppins',
            'is_active' => true,
        ];
    }
}

