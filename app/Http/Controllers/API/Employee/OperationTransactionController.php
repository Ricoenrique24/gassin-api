<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\OperationTransaction;
use App\Models\PurchaseTransaction;
use App\Models\ResupplyTransaction;
use App\Models\CategoryTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use App\Service\NotificationService;
use Validator;

class OperationTransactionController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $apiKey = $request->bearerToken();
        $user = User::where('apikey', $apiKey)->first();

        if (!$user) {
            return response()->json([
                "error" => true,
                "message" => "Unauthorized",
            ]);
        }

        $validator = Validator::make($request->all(), [
            'id_transaction' => 'required',
            'total_payment' => 'required|numeric',
            'note' => 'required|string',
            'type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => true,
                "message" => $validator->errors(),
            ]);
        }

        $id_transaction = $request->input('id_transaction');
        $type = $request->input('type');
        $category_transaction = CategoryTransaction::where('name', $type)->first();

        if (!$category_transaction) {
            return response()->json([
                "error" => true,
                'message' => 'Invalid transaction type'
            ]);
        }

        switch ($category_transaction->id) {
            case 1:
                $transaction = PurchaseTransaction::with('statusTransaction', 'user', 'customer')
                                    ->findOrFail($id_transaction);
                break;
            case 2:
                $transaction = ResupplyTransaction::with('statusTransaction', 'user', 'store')
                                    ->findOrFail($id_transaction);
                break;
            default:
                return response()->json([
                    "error" => true,
                    'message' => 'Invalid transaction type'
                ]);
        }

        if ($transaction) {
            $transaction->id_user = $user->id;
            $transaction->total_payment = $request->input('total_payment');
            $transaction->note = $request->input('note');
            $transaction->id_category_transaction = $category_transaction->id;

            $operation_transaction = OperationTransaction::create([
                'id_transaction' => $transaction->id,
                'id_user' => $transaction->id_user,
                'total_payment' => $transaction->total_payment,
                'note' => $transaction->note,
                'id_category_transaction' => $transaction->id_category_transaction,
            ]);

            // Mengambil semua manager
            $managers = User::where('role', 'manager')->get();

            // Mengirim notifikasi ke setiap manager
            foreach ($managers as $manager) {
                $managerToken = $manager->token_fcm;

                $title = 'Pengajuan Biaya Opersional Baru';
                $body = 'Karyawan mengajukan biaya operasional';
                $contentData = [
                    'transaction_id' => $operation_transaction->id,
                    'type' => $type,
                    // Informasi tambahan sesuai kebutuhan
                ];

                // Mengirim notifikasi ke pengguna
                $this->notificationService->sendNotificationToSpecificToken($managerToken, $title, $body, $contentData);
            }

            return response()->json([
                "error" => false,
                "message" => "Biaya Operasional berhasil diajukan"
            ]);
        }
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

        $type = $request->input('type');
        $category_transaction = CategoryTransaction::where('name', $type)->first();

        if (!$category_transaction) {
            return response()->json([
                "error" => true,
                'message' => 'Invalid transaction type'
            ]);
        }

        $operation_transaction = OperationTransaction::where('id_category_transaction', $category_transaction->id)
                                    ->where('id_transaction', $id)
                                    ->where('id_user', $user->id)
                                    ->first();

        if (!$operation_transaction) {
            return response()->json([
                "error" => true,
                'message' => 'Operation transaction not found'
            ]);
        }

        switch ($category_transaction->id) {
            case 1:
                $transaction = PurchaseTransaction::with('statusTransaction', 'user', 'customer')
                                    ->findOrFail($id);
                $operation_transaction->purchase = $transaction;
                break;
            case 2:
                $transaction = ResupplyTransaction::with('statusTransaction', 'user', 'store')
                                    ->findOrFail($id);
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
}
