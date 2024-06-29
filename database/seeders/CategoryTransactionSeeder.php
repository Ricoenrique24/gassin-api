<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoryTransaction;


class CategoryTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Category Transaction
        CategoryTransaction::create([
            'id' => 1,
            'name' => 'purchase',
        ]);
        CategoryTransaction::create([
            'id' => 2,
            'name' => 'resupply',
        ]);
    }
}
