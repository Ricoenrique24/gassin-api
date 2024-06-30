<?php

namespace App\Http\Controllers\API\Manager;

use App\Http\Controllers\Controller;
use App\Service\NotificationService;
use App\Models\ResupplyTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ResupplyTransactionController extends Controller
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
        $resupplyTransactions = ResupplyTransaction::with('statusTransaction', 'user', 'store')->get();

        return response()->json([
            "error" => false,
            "message" => "Resupply Transactions fetched successfully",
            "listResupply" => $resupplyTransactions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $request->validate([
            'id_store' => 'required|exists:stores,id',
            'id_user' => 'required|exists:users,id',
            'qty' => 'required|integer',
            'total_payment' => 'required|numeric'
        ]);
        $input = $request->all();
        $input['status'] = 1;

        $resupplyTransaction = ResupplyTransaction::create($input);

        $employee = User::find($request->id_user);
        $employeeToken = $employee->token_fcm;

        $title = 'New Resupply Transaction';
        $body = 'You have a new resupply transaction to handle.';
        $contentData = [
            'transaction_id' => $resupplyTransaction->id,
            'type' => 'resupply',
        ];

        // Mengirim notifikasi ke pengguna
        $this->notificationService->sendNotificationToSpecificToken($employeeToken, $title, $body, $contentData);

        return response()->json([
            "error" => false,
            "message" => "Resupply Transaction created successfully",
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $resupplyTransaction = ResupplyTransaction::with('statusTransaction', 'user', 'store')
        ->find($id);

        if (!$resupplyTransaction) {
            return response()->json([
                "error" => true,
                'message' => 'Resupply Transaction not found'
            ]);
        }

        return response()->json([
            "error" => false,
            "message" => "Resupply Transaction fetched successfully",
            "resupply" => $resupplyTransaction
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $resupplyTransaction = ResupplyTransaction::find($id);

        if (!$resupplyTransaction) {
            return response()->json(['message' => 'Resupply Transaction not found'], 404);
        }
        $validated = $request->validate([
            'id_store' => 'sometimes|required',
            'id_user' => 'sometimes|required',
            'qty' => 'sometimes|required|integer',
            'total_payment' => 'sometimes|required|numeric'
        ]);

        $resupplyTransaction->update($validated);

        return response()->json([
            "error" => false,
            "message" => "Resupply Transaction updated successfully",
            "resupply" => $resupplyTransaction
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $resupplyTransaction = ResupplyTransaction::find($id);

        if (!$resupplyTransaction) {
            return response()->json([
                "error" => true,
                'message' => 'Resupply Transaction not found'
            ]);
        }
        $resupplyTransaction->delete();

        return response()->json([
            "error" => false,
            'message' => 'Resupply Transaction deleted successfully'
        ]);
    }

    public function search(Request $request)
    {
        try {
            $keyword = $request->input('q');

            $resupplyTransactions = ResupplyTransaction::search($keyword);

            return response()->json([
                "error" => false,
                "message" => "Resupply Transactions fetched successfully",
                "listResupply" => $resupplyTransactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => true,
                "message" => "Failed to fetch Resupply Transactions: " . $e->getMessage()
            ], 500);
        }
    }
    public function filter(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'filterBy' => 'required|string|in:day,week,month,all'
        ]);

        $statusTransaction = $validated['status'];
        $filterBy = $validated['filterBy'];

        try {
            $query = ResupplyTransaction::with('statusTransaction', 'user', 'store');

            // Hanya tambahkan kondisi filter status jika status tidak "all"
            if ($statusTransaction !== "all") {
                $query->where('status', $statusTransaction);
            }

            switch ($filterBy) {
                case "day":
                    $today = Carbon::today();
                    $query->whereDate('created_at', $today);
                    break;
                case "week":
                    $startOfWeek = Carbon::now()->startOfWeek();
                    $endOfWeek = Carbon::now()->endOfWeek();
                    $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                    break;
                case "month":
                    $startOfMonth = Carbon::now()->startOfMonth();
                    $endOfMonth = Carbon::now()->endOfMonth();
                    $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                    break;
                case "all":
                default:
                    // Tidak ada filter tambahan untuk "all" atau default
                    break;
            }

            $resupplyTransactions = $query->get();

            return response()->json([
                'error' => false,
                'message' => 'Resupply Transactions fetched successfully',
                'listResupply' => $resupplyTransactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to fetch Resupply Transactions: ' . $e->getMessage()
            ], 500);
        }
    }
    public function cancelled(Request $request, $id)
    {
        $resupplyTransaction = ResupplyTransaction::with('statusTransaction', 'user', 'store')->findOrFail($id);

        if (!$resupplyTransaction) {
            return response()->json([
                "error" => true,
                "message" => "Resupply Transaction not found"
            ]);
        }

        // Update status transaction
        $resupplyTransaction->status = 4;
        $resupplyTransaction->save();

        $idEmployee = $resupplyTransaction->user->id;
        // Mengambil employee
        $employee = User::find($idEmployee);

        if (!$employee) {
            return response()->json([
                "error" => true,
                "message" => "Employee not found"
            ], 404);
        }

        // Mengirim notifikasi ke setiap employee
        $employeeToken = $employee->token_fcm;

        $title = 'Pesanan Di Perbarui';
        $body = 'You have a new resupply transaction to handle.';
        $contentData = [
            'transaction_id' => $resupplyTransaction->id,
            'type' => 'resupply',
            // Informasi tambahan sesuai kebutuhan
        ];

        // Mengirim notifikasi ke pengguna
        $this->notificationService->sendNotificationToSpecificToken($employeeToken, $title, $body, $contentData);

        return response()->json([
            "error" => false,
            "message" => "Pesanan berhasil diperbarui"
        ]);
    }
}
