<?php

namespace App\Http\Controllers\API\Manager;

use App\Http\Controllers\Controller;
use App\Service\NotificationService;
use Illuminate\Http\Request;
use App\Models\PurchaseTransaction;
use App\Models\User;

class PurchaseTransactionController extends Controller
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
        $purchaseTransactions = PurchaseTransaction::with('statusTransaction', 'user', 'customer')->get();

        return response()->json([
            "error" => false,
            "message" => "Purchase Transactions fetched successfully",
            "listPurchase" => $purchaseTransactions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $request->validate([
            'id_customer' => 'required|exists:customers,id',
            'id_user' => 'required|exists:users,id',
            'qty' => 'required|integer',
            'total_payment' => 'required|numeric'
        ]);
        $input = $request->all();
        $input['status'] = 1;

        $purchaseTransaction = PurchaseTransaction::create($input);

        $employee = User::find($request->id_user);
        $employeeToken = $employee->token_fcm;

        $title = 'New Purchase Transaction';
        $body = 'You have a new purchase transaction to handle.';
        $contentData = [
            'transaction_id' => $purchaseTransaction->id,
            'type' => 'purchase',
            // Informasi tambahan sesuai kebutuhan
        ];

        // Mengirim notifikasi ke pengguna
        $this->notificationService->sendNotificationToSpecificToken($employeeToken, $title, $body, $contentData);

        return response()->json([
            "error" => false,
            "message" => "Purchase Transaction created successfully",
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $purchaseTransaction = PurchaseTransaction::with('statusTransaction', 'user', 'customer')
        ->find($id);

        if (!$purchaseTransaction) {
            return response()->json([
                "error" => true,
                'message' => 'Purchase Transaction not found'
            ]);
        }

        return response()->json([
            "error" => false,
            "message" => "Purchase Transaction fetched successfully",
            "purchase" => $purchaseTransaction
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $purchaseTransaction = PurchaseTransaction::find($id);

        if (!$purchaseTransaction) {
            return response()->json(['message' => 'Purchase Transaction not found'], 404);
        }
        $validated = $request->validate([
            'id_customer' => 'sometimes|required',
            'id_user' => 'sometimes|required',
            'qty' => 'sometimes|required|integer',
            'total_payment' => 'sometimes|required|numeric',
            'status' => 'sometimes|required',
            'note' => 'nullable|string'
        ]);

        $purchaseTransaction->update($validated);

        $employee = User::find($request->id_user);
        $employeeToken = $employee->token_fcm;

        $title = 'Pesanan Di Perbarui';
        $body = 'You have a new purchase transaction to handle.';
        $contentData = [
            'transaction_id' => $purchaseTransaction->id,
            'type' => 'purchase',
            // Informasi tambahan sesuai kebutuhan
        ];

        // Mengirim notifikasi ke pengguna
        $this->notificationService->sendNotificationToSpecificToken($employeeToken, $title, $body, $contentData);

        return response()->json([
            "error" => false,
            "message" => "Purchase Transaction updated successfully",
            "purchase" => $purchaseTransaction
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $purchaseTransaction = PurchaseTransaction::find($id);

        if (!$purchaseTransaction) {
            return response()->json([
                "error" => true,
                'message' => 'Purchase Transaction not found'
            ]);
        }
        $purchaseTransaction->delete();

        return response()->json([
            "error" => false,
            'message' => 'Purchase Transaction deleted successfully'
        ]);
    }

    public function search(Request $request)
{
    try {
        $keyword = $request->input('q');

        $purchaseTransactions = PurchaseTransaction::search($keyword);

        return response()->json([
            "error" => false,
            "message" => "Purchase Transactions fetched successfully",
            "listPurchase" => $purchaseTransactions
        ]);
    } catch (\Exception $e) {
        return response()->json([
            "error" => true,
            "message" => "Failed to fetch Purchase Transactions: " . $e->getMessage()
        ], 500);
    }
}

}
