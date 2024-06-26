<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\PurchaseTransaction;
use App\Models\ResupplyTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $apiKey = $request->bearerToken();
        $user = User::where('apikey', $apiKey)->first();

        if (!$user) {
            return response()->json([
                "error" => true,
                "message" => "Unauthorized",
            ]);
        }

        // Mengambil data PurchaseTransaction dan ResupplyTransaction dengan memuat relasi
        $purchaseTransactions = PurchaseTransaction::with('statusTransaction', 'user', 'customer')
            ->where('id_user', $user->id)
            ->get()
            ->map(function ($transaction) {
                $transaction->type = 'purchase';
                return $transaction;
            });

        $resupplyTransactions = ResupplyTransaction::with('statusTransaction', 'user', 'store')
            ->where('id_user', $user->id)
            ->get()
            ->map(function ($transaction) {
                $transaction->type = 'resupply';
                return $transaction;
            });

        // Menggabungkan dan menyatukan hasil dari kedua query
        $transactions = $purchaseTransactions->concat($resupplyTransactions);

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
        $apiKey = $request->bearerToken();
        $user = User::where('apikey', $apiKey)->first();

        if (!$user) {
            return response()->json([
                "error" => true,
                "message" => "Unauthorized",
            ]);
        }

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

}
