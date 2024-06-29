<?php

namespace App\Http\Controllers\API\Manager;

use App\Http\Controllers\Controller;
use App\Models\OperationTransaction;
use App\Models\PurchaseTransaction;
use App\Models\ResupplyTransaction;
use App\Models\CategoryTransaction;
use App\Service\NotificationService;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;

class OperationTransactionController extends Controller
{
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $operationTransactions = OperationTransaction::with('categoryTransaction')->get();

        $operationTransactions->each(function ($operationTransaction) {
            switch ($operationTransaction->id_category_transaction) {
                case 1:
                    $transaction = PurchaseTransaction::with('statusTransaction', 'user', 'customer')
                                        ->find($operationTransaction->id_transaction);
                    $operationTransaction->purchase = $transaction;
                    break;
                case 2:
                    $transaction = ResupplyTransaction::with('statusTransaction', 'user', 'store')
                                        ->find($operationTransaction->id_transaction);
                    $operationTransaction->resupply = $transaction;
                    break;
                default:
                    break;
            }
        });
        return response()->json([
            'error' => false,
            'message' => 'Operation Transactions fetched successfully',
            'listOperation' => $operationTransactions
            ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $type = $request->input('type');
        $category_transaction = CategoryTransaction::where('name', $type)->first();

        if (!$category_transaction) {
            return response()->json([
                "error" => true,
                'message' => 'Invalid transaction type'
            ]);
        }

        $operation_transaction = OperationTransaction::with('categoryTransaction')->where('id', $id)->first();

        if (!$operation_transaction) {
            return response()->json([
                "error" => true,
                'message' => 'Operation transaction not found'
            ]);
        }

        $idTransaction = $operation_transaction->id_transaction;

        switch ($category_transaction->id) {
            case 1:
                $transaction = PurchaseTransaction::with('statusTransaction', 'user', 'customer')
                                    ->findOrFail($idTransaction);
                $operation_transaction->purchase = $transaction;
                break;
            case 2:
                $transaction = ResupplyTransaction::with('statusTransaction', 'user', 'store')
                                    ->findOrFail($idTransaction);
                $operation_transaction->resupply = $transaction;
                break;
            default:
                return response()->json([
                    "error" => true,
                    'message' => 'Invalid transaction type'
                ]);
        }

        return response()->json([
            "error" => false,
            "message" => "Operation Transaction fetched successfully",
            "operation_transaction" => $operation_transaction
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $type = $request->input('type');
        $category_transaction = CategoryTransaction::where('name', $type)->first();

        if (!$category_transaction) {
            return response()->json([
                "error" => true,
                'message' => 'Invalid transaction type'
            ]);
        }

        $operation_transaction = OperationTransaction::with('categoryTransaction')->where('id', $id)->first();

        if (!$operation_transaction) {
            return response()->json([
                "error" => true,
                'message' => 'Operation transaction not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'verified' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => true,
                "message" => $validator->errors(),
                "verified" => $request->input('verified')
            ]);
        }

        // Mengupdate status verified
        $operation_transaction->verified = $request->input('verified');
        $operation_transaction->save();

        return response()->json([
            "error" => false,
            "message" => "Operation Transaction updated successfully"
        ]);
    }

    public function search(Request $request)
    {
        try {
            $keyword = $request->input('q');

            $operation_transaction = OperationTransaction::search($keyword);

            return response()->json([
                "error" => false,
                "message" => "Operation Transactions fetched successfully",
                "listOperation" => $operation_transaction
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => true,
                "message" => "Failed to fetch Operation Transactions: " . $e->getMessage()
            ], 500);
        }
    }
}
