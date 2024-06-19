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
            'status' => 'Menunggu konfirmasi karyawan',
        ]);
        StatusTransaction::create([
            'id' => 2,
            'status' => 'Telah dikonfirmasi oleh karyawan',
        ]);
        StatusTransaction::create([
            'id' => 3,
            'status' => 'Karyawan sedang dalam perjalanan menuju mitra untuk membeli gas',
        ]);
        StatusTransaction::create([
            'id' => 4,
            'status' => 'Karyawan telah sampai di lokasi mitra',
        ]);
        StatusTransaction::create([
            'id' => 5,
            'status' => 'Karyawan berhasil membeli gas dari mitra',
        ]);
        StatusTransaction::create([
            'id' => 6,
            'status' => 'Karyawan sedang dalam perjalanan kembali ke toko',
        ]);
        StatusTransaction::create([
            'id' => 7,
            'status' => 'Karyawan telah kembali ke toko',
        ]);
        StatusTransaction::create([
            'id' => 8,
            'status' => 'Karyawan sedang dalam perjalanan menuju lokasi pelanggan',
        ]);
        StatusTransaction::create([
            'id' => 9,
            'status' => 'Karyawan telah sampai di lokasi pelanggan',
        ]);
        StatusTransaction::create([
            'id' => 10,
            'status' => 'Gas telah berhasil diserahkan kepada pelanggan',
        ]);
        StatusTransaction::create([
            'id' => 11,
            'status' => 'Transaksi selesai',
        ]);
        StatusTransaction::create([
            'id' => 12,
            'status' => 'Transaksi dibatalkan',
        ]);
    }
}
