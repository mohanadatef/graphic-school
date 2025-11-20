<?php

namespace Database\Factories;

use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use App\Contracts\Services\PasswordHasherInterface;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $passwordHasher = app(PasswordHasherInterface::class);
        
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => $passwordHasher->hash('password'), // SOLID - Use interface
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'role_id' => Role::factory(),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::factory()->create(['name' => 'admin'])->id,
        ]);
    }

    public function instructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::factory()->create(['name' => 'instructor'])->id,
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::factory()->create(['name' => 'student'])->id,
        ]);
    }
}
