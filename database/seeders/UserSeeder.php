<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds for the `users` table.
     * Creates 100 users with varied roles, statuses, and realistic data.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('fr_BE');
        $roles = ['USER', 'ADMIN', 'PROVIDER'];
        $languages = ['fr', 'nl', 'en'];

        for ($i = 1; $i <= 100; $i++) {
            $role = $faker->randomElement($roles);
            $isProvider = ($role === 'PROVIDER');

            User::create([
                'last_name' => $faker->lastName,
                'first_name' => $faker->firstName,
                'email' => 'user' . $i . '@example.com',
                'password' => Hash::make('password'),
                'address' => $faker->boolean(80) ? $faker->address : null,
                'mobile_phone' => $faker->boolean(70) ? '+32' . $faker->numerify('4## ### ###') : null,
                'vat_number' => $isProvider && $faker->boolean(60) ? 'BE' . $faker->numerify('##########') : null,
                'website' => $isProvider && $faker->boolean(50) ? $faker->url : null,
                'language' => $faker->randomElement($languages),
                'role' => $role,
                'is_banned' => $faker->boolean(10),
                'is_confirmed' => $faker->boolean(90),
                'registration_date' => $faker->dateTimeBetween('-1 year', 'now'),
            ]);
        }

        $this->command->info('✅ 100 users created successfully.');
    }
}
