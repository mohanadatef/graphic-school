<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Categories\Models\Category;
use App\Support\Table\TableBuilder;
use Illuminate\Http\Request;

class TableBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_builder_can_paginate(): void
    {
        Course::factory()->count(25)->create();

        $request = Request::create('/api/admin/courses', 'GET', [
            'per_page' => 10,
            'page' => 1,
        ]);

        $query = Course::query();
        $builder = new TableBuilder($query, $request);
        $result = $builder->build();

        $this->assertCount(10, $result->items());
        $this->assertEquals(25, $result->total());
    }

    public function test_table_builder_can_search(): void
    {
        Course::factory()->create(['title' => 'Test Course']);
        Course::factory()->create(['title' => 'Another Course']);

        $request = Request::create('/api/admin/courses', 'GET', [
            'search' => 'Test',
        ]);

        $query = Course::query();
        $builder = new TableBuilder($query, $request);
        $result = $builder->build();

        $this->assertGreaterThan(0, $result->count());
        $this->assertTrue(
            $result->items()->contains(fn($course) => str_contains($course->title, 'Test'))
        );
    }

    public function test_table_builder_can_sort(): void
    {
        Course::factory()->create(['title' => 'A Course', 'price' => 100]);
        Course::factory()->create(['title' => 'B Course', 'price' => 200]);
        Course::factory()->create(['title' => 'C Course', 'price' => 300]);

        $request = Request::create('/api/admin/courses', 'GET', [
            'sort_by' => 'price',
            'sort_order' => 'desc',
        ]);

        $query = Course::query();
        $builder = new TableBuilder($query, $request);
        $result = $builder->build();

        $prices = $result->items()->pluck('price')->toArray();
        $this->assertEquals([300, 200, 100], $prices);
    }

    public function test_table_builder_respects_max_per_page(): void
    {
        Course::factory()->count(100)->create();

        $request = Request::create('/api/admin/courses', 'GET', [
            'per_page' => 1000, // Exceeds max
        ]);

        $query = Course::query();
        $builder = new TableBuilder($query, $request);
        
        // Should throw validation error or cap at max
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $builder->build();
    }
}

