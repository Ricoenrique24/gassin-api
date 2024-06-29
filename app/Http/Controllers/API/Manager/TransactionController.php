<?php

namespace App\Http\Controllers\API\Manager;

use App\Http\Controllers\Controller;
use App\Models\PurchaseTransaction;
use App\Models\ResupplyTransaction;
use App\Service\NotificationService;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil data PurchaseTransaction dan ResupplyTransaction dengan memuat relasi
        $purchaseTransactions = PurchaseTransaction::with('statusTransaction', 'user', 'customer')
            ->whereNot('status', '3')
            ->whereNot('status', '4')
            ->get()
            ->map(function ($transaction) {
                $transaction->type = 'purchase';
                return $transaction;
            });

        $resupplyTransactions = ResupplyTransaction::with('statusTransaction', 'user', 'store')
            ->whereNot('status', '3')
            ->whereNot('status', '4')
            ->get()
            ->map(function ($transaction) {
                $transaction->type = 'resupply';
                return $transaction;
            });

        // Menggabungkan dan menyatukan hasil dari kedua query
        $transactions = $purchaseTransactions->concat($resupplyTransactions)->sortByDesc('created_at')->values();

        return response()->json([
            "error" => false,
            "message" => "Transactions fetched successfully",
            "listTransaction" => $transactions
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $type = $request->input('type'); // Ambil jenis transaksi dari parameter request

        switch ($type) {
            case 'purchase':
                $transaction = PurchaseTransaction::with('statusTransaction', 'user', 'customer')
                                    ->findOrFail($id);
                $transaction->type = 'purchase';
                break;
            case 'resupply':
                $transaction = ResupplyTransaction::with('statusTransaction', 'user', 'store')
                                    ->findOrFail($id);
                $transaction->type = 'resupply';
                break;
            default:
                return response()->json([
                    "error" => true,
                    'message' => 'Invalid transaction type'
                ]);
        }

        if ($transaction) {
            return response()->json([
                "error" => false,
                "message" => "Transactions fetched successfully",
                "transaction" => $transaction
            ]);
        }

        // Jika tidak ada transaksi dengan ID yang sesuai, kembalikan response error
        return response()->json([
            "error" => true,
            'message' => 'Transaction not found'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseTransaction $purchaseTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseTransaction $purchaseTransaction)
    {
        //
    }
}
