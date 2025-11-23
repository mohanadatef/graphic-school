<?php

namespace Tests\Feature\Api\Phase4;

use Tests\TestCase;
use App\Models\CalendarEvent;
use Modules\ACL\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DynamicLearningSeeder::class);
    }

    public function test_user_can_get_calendar_events(): void
    {
        $user = User::first();

        CalendarEvent::create([
            'user_id' => $user->id,
            'event_type' => 'custom',
            'title' => 'Test Event',
            'start_datetime' => Carbon::now()->addDays(1),
            'end_datetime' => Carbon::now()->addDays(1)->addHours(2),
            'color' => '#3b82f6',
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/calendar', [
                'start' => Carbon::now()->toDateString(),
                'end' => Carbon::now()->addDays(30)->toDateString(),
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'title', 'start', 'end', 'color']],
            ]);
    }

    public function test_user_can_create_custom_event(): void
    {
        $user = User::first();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/calendar', [
                'title' => 'Custom Event',
                'description' => 'Event description',
                'start_datetime' => Carbon::now()->addDays(1)->toDateTimeString(),
                'end_datetime' => Carbon::now()->addDays(1)->addHours(2)->toDateTimeString(),
                'color' => '#10b981',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'start_datetime', 'end_datetime'],
            ]);

        $this->assertDatabaseHas('calendar_events', [
            'user_id' => $user->id,
            'title' => 'Custom Event',
            'event_type' => 'custom',
        ]);
    }
}

