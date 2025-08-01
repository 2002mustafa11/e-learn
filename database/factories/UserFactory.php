<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = $this->faker->randomElement(['student', 'parent', 'teacher', 'admin']);

        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
            'last_login_at' => now(),
            'device_token' => Str::random(32),
            'last_login_ip' => $this->faker->ipv4,
        ];
    }

    public function student()
    {
        return $this->state(fn () => ['role' => 'student']);
    }

    public function parent()
    {
        return $this->state(fn () => ['role' => 'parent']);
    }

    public function teacher()
    {
        return $this->state(fn () => ['role' => 'teacher']);
    }

    public function admin()
    {
        return $this->state(fn () => ['role' => 'admin']);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
