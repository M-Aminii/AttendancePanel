<?php

namespace Database\Factories;

use App\Models\User;
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
        return [
            'mobile' => '09157015018',
            'email'=>$this->faker->unique()->safeEmail,
            'name'=>'مصطفی',
            'last_name'=>'امینی',
            'username'=>$this->faker->userName,
            'password'=> '$2y$12$4JBC3Xk1oH3B9mMOpj5K1.Zb24LLbyN/7JDUuBLSKRv4YsprDkwIa', // M123456
            'avatar'=> null,
        ];
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
