<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 5; $i++) {
            $name = $faker->firstName . ' ' . $faker->lastName;
            $phone = $faker->phoneNumber;
            $address = $faker->address;
            $linkMap = $faker->streetAddress;
            $price = 21000;

            Store::create([
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'link_map' => $linkMap,
                'price' => $price
            ]);
        }
    }
}
