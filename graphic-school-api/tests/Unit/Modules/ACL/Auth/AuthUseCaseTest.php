<?php

namespace Tests\Unit\Modules\ACL\Auth;

use Tests\TestCase;
use Modules\ACL\Auth\Application\UseCases\RegisterUserUseCase;
use Modules\ACL\Auth\Application\DTOs\RegisterUserDTO;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_user(): void
    {
        $role = Role::factory()->create(['name' => 'student']);

        $dto = RegisterUserDTO::fromArray([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'phone' => '1234567890',
            'address' => 'Test Address',
        ]);

        $useCase = app(RegisterUserUseCase::class);
        $result = $useCase->execute($dto);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertEquals('Test User', $result['user']->name);
    }
}

