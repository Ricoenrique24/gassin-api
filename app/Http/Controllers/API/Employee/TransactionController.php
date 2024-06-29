<?php

namespace App\Http\Controllers\API\Employee;

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
    public function active(Request $request)
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
            ->whereNot('status', '3')
            ->whereNot('status', '4')
            ->get()
            ->map(function ($transaction) {
                $transaction->type = 'purchase';
                return $transaction;
            });

        $resupplyTransactions = ResupplyTransaction::with('statusTransaction', 'user', 'store')
            ->where('id_user', $user->id)
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
     * Update the specified resource in storage.
     */
    public function inProgress(Request $request, $id)
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
                break;
            case 'resupply':
                $transaction = ResupplyTransaction::with('statusTransaction', 'user', 'store')
                                    ->findOrFail($id);
                break;
            default:
                return response()->json([
                    "error" => true,
                    'message' => 'Invalid transaction type'
                ]);
        }

        if (!$transaction) {
            return response()->json([
                "error" => true,
                "message" => "Transaction not found"
            ]);
        }

        // Update status transaction
        $transaction->status = 2;
        $transaction->save();

        // Mengambil semua manager
        $managers = User::where('role', 'manager')->get();

        // Mengirim notifikasi ke setiap manager
        foreach ($managers as $manager) {
            $managerToken = $manager->token_fcm;

            $title = 'Pesanan Sedang Dikerjakan';
            $body = 'Karyawan sedang mengerjakan pesanan';
            $contentData = [
                'transaction_id' => $transaction->id,
                'type' => $type,
                // Informasi tambahan sesuai kebutuhan
            ];

            // Mengirim notifikasi ke pengguna
            $this->notificationService->sendNotificationToSpecificToken($managerToken, $title, $body, $contentData);
        }

        return response()->json([
            "error" => false,
            "message" => "Transaksi berhasil diperbarui",
            "transaction" => $transaction
        ]);
    }

    public function completed(Request $request, $id)
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
                break;
            case 'resupply':
                $transaction = ResupplyTransaction::with('statusTransaction', 'user', 'store')
                                    ->findOrFail($id);
                break;
            default:
                return response()->json([
                    "error" => true,
                    'message' => 'Invalid transaction type'
                ]);
        }

        if (!$transaction) {
            return response()->json([
                "error" => true,
                "message" => "Transaction not found"
            ]);
        }

        // Update status transaction
        $transaction->status = 3;
        $transaction->save();

        // Mengambil semua manager
        $managers = User::where('role', 'manager')->get();

        // Mengirim notifikasi ke setiap manager
        foreach ($managers as $manager) {
            $managerToken = $manager->token_fcm;

            $title = 'Pesanan Selesai';
            $body = 'Karyawan sudah mengerjakan pesanan';
            $contentData = [
                'transaction_id' => $transaction->id,
                'type' => $transaction->type,
                // Informasi tambahan sesuai kebutuhan
            ];

            // Mengirim notifikasi ke pengguna
            $this->notificationService->sendNotificationToSpecificToken($managerToken, $title, $body, $contentData);
        }

        return response()->json([
            "error" => false,
            "message" => "Transaksi berhasil diperbarui",
            "transaction" => $transaction
        ]);
    }
    public function cancelled(Request $request, $id)
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
                break;
            case 'resupply':
                $transaction = ResupplyTransaction::with('statusTransaction', 'user', 'store')
                                    ->findOrFail($id);
                break;
            default:
                return response()->json([
                    "error" => true,
                    'message' => 'Invalid transaction type'
                ]);
        }

        if (!$transaction) {
            return response()->json([
                "error" => true,
                "message" => "Transaction not found"
            ]);
        }

        // Update status transaction
        $transaction->status = 4;
        $transaction->note = $request->note;
        $transaction->save();

        // Mengambil semua manager
        $managers = User::where('role', 'manager')->get();

        // Mengirim notifikasi ke setiap manager
        foreach ($managers as $manager) {
            $managerToken = $manager->token_fcm;

            $title = 'Pesanan Dibatalkan';
            $body = 'Karyawan terkendala dalam mengerjakan pesanan';
            $contentData = [
                'transaction_id' => $transaction->id,
                'type' => $transaction->type,
                // Informasi tambahan sesuai kebutuhan
            ];

            // Mengirim notifikasi ke pengguna
            $this->notificationService->sendNotificationToSpecificToken($managerToken, $title, $body, $contentData);
        }

        return response()->json([
            "error" => false,
            "message" => "Transaksi berhasil diperbarui",
            "transaction" => $transaction
        ]);
    }
}
