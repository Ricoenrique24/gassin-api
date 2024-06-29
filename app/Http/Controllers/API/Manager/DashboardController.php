<?php

namespace App\Http\Controllers\API\Manager;

use App\Http\Controllers\Controller;
use App\Models\ResupplyTransaction;
use App\Models\PurchaseTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAvailableStockQuantity()
    {
         // Menghitung total gas masuk dari transaksi resupply yang sudah selesai
         $totalIn = ResupplyTransaction::where('status', '3')->sum('qty');

         // Menghitung total gas keluar dari transaksi pembelian yang sudah selesai
         $totalOut = PurchaseTransaction::where('status', '3')->sum('qty');

         // Menghitung total gas yang sedang dalam proses pengantaran atau resupply
         $totalInProcess = ResupplyTransaction::where('status', '2')->sum('qty');
         $totalOutProcess = PurchaseTransaction::where('status', '2')->sum('qty');

         // Menghitung total gas yang dibatalkan dari resupply
         $totalCancelledIn = ResupplyTransaction::where('status', '4')->sum('qty');

         // Menghitung total gas yang dibatalkan dari pembelian customer
         $totalCancelledOut = PurchaseTransaction::where('status', '4')->sum('qty');

         // Stok terkini di gudang termasuk yang sedang dalam proses dan yang dibatalkan
         $currentStock = $totalIn - $totalOut - $totalOutProcess + $totalCancelledOut;

        return response()->json([
            'error' => false,
            'message' => 'Available Stock Quantity fetched successfully',
            'stock' => $currentStock
        ]);
    }

    public function getRevenueToday()
    {
        // Mendapatkan tanggal hari ini
        $today = Carbon::today();

        // Menghitung total pendapatan dari transaksi pembelian yang selesai pada hari ini
        $dailyRevenue = PurchaseTransaction::where('status', '3')
                        ->whereDate('created_at', $today)
                        ->sum('total_payment');

        // Mengembalikan hasil dalam format JSON
        return response()->json([
            'error' => false,
            'message' => 'Revenue Today fetched successfully',
            'dailyRevenue' => $dailyRevenue,
        ]);
    }
}
