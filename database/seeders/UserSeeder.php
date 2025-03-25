<?php

namespace Database\Seeders;

use App\Enums\UserTypes;
use Illuminate\Database\Seeder;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create 50 users
        foreach (range(1, 50) as $index) {
            $name = $faker->firstName . ' ' . $faker->lastName;
            $password = bcrypt($name . $faker->regexify('[a-zA-Z0-9]{10}')); // Password is name + random string

            User::create([
                'name' => $name,
                'email' => $faker->unique()->safeEmail,
                'user_type' => $faker->randomElement(UserTypes::cases()),
                'address' => $faker->address,
                'city' => $faker->city,
                'postal_code' => $faker->postcode,
                'password' => $password, // Hashed password
            ]);
        }
    }
}
