<?php

namespace Database\Factories;

use App\Enums\UserTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $name = $this->faker->name();
        $postal_code = $this->faker->postcode();
        $city = $this->faker->city();

        return [
            'name' => $name,
            'email' => $this->faker->unique()->safeEmail(),
            'user_type' => $this->faker->randomElement(UserTypes::cases())->value,
            'address' => $this->faker->address(),
            'city' => $city,
            'postal_code' => $postal_code,
            'password' => "$name$city$postal_code",
        ];
    }

}
