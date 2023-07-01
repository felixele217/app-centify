<?php

namespace Database\Factories;

use App\Enum\RoleEnum;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'organization_id' => Organization::factory()->create(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function agent(): static
    {
        return $this->state(fn (array $attributes) => [
            'base_salary' => fake()->randomElement([5000000, 10000000]),
            'on_target_earning' => fake()->randomElement([10000000, 20000000]),
            'role' => RoleEnum::AGENT->value, // internal flag
        ]);
    }

    public function configure()
    {
        return $this->afterMaking(function ($user) {
            if (isset($user->role)) {
                $role = Role::firstOrCreate(['name' => $user->role]);

                $user->assignRole($role);

                unset($user->role);
            }
        });
    }
}
