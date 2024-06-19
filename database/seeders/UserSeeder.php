<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Seed Manager Account
        User::create([
            'name' => 'Manager',
            'username' => 'manager',
            'email' => 'ahnafbawedan01@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '123456789',
            'role' => 'manager',
            'apikey' => 'manager_api_key',
            'token_fcm' => 'manager_token_fcm',
        ]);

        // Seed Employee Accounts
        for ($i = 1; $i <= 5; $i++) {
            $name = $faker->firstName . ' ' . $faker->lastName; // Generate random full name
            $username = 'employee' . $i;

            User::create([
                'name' => $name,
                'username' => $username,
                'email' => strtolower($name) . $i . '@example.com',
                'password' => bcrypt('password'),
                'phone' => '08123456789' . $i . '0',
                'role' => 'employee',
                'apikey' => Str::random(60),
                'token_fcm' => 'employee' . $i . '_token_fcm',
            ]);
        }
    }
}
