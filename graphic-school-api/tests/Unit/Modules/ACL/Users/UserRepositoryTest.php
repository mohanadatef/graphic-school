<?php

namespace Tests\Unit\Modules\ACL\Users;

use Tests\TestCase;
use Modules\ACL\Users\Infrastructure\Repositories\Eloquent\UserRepository;
use Modules\ACL\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository();
    }

    public function test_can_create_user(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
        ];

        $user = $this->repository->create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    public function test_can_find_user_by_id(): void
    {
        $user = User::factory()->create();

        $found = $this->repository->find($user->id);

        $this->assertInstanceOf(User::class, $found);
        $this->assertEquals($user->id, $found->id);
    }

    public function test_can_find_user_by_email(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $found = $this->repository->findByEmail('test@example.com');

        $this->assertInstanceOf(User::class, $found);
        $this->assertEquals($user->id, $found->id);
    }

    public function test_can_update_user(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $updated = $this->repository->update($user, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
    }

    public function test_can_delete_user(): void
    {
        $user = User::factory()->create();

        $result = $this->repository->delete($user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}

