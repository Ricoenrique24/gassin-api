<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StatusTransaction;

class StatusTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Status Transaction
        StatusTransaction::create([
            'id' => 1,
            'status' => 'pending',
        ]);
        StatusTransaction::create([
            'id' => 2,
            'status' => 'in_progress',
        ]);
        StatusTransaction::create([
            'id' => 3,
            'status' => 'completed',
        ]);
        StatusTransaction::create([
            'id' => 4,
            'status' => 'cancelled',
        ]);
    }
}
