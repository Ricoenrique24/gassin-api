<?php

namespace App\Http\Controllers\API\Manager;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            "error" => false,
            "message" => "Stores fetched successfully",
            "listStore" => Store::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'link_map' => 'string',
            'price' => 'required|numeric',
        ]);

        $store = Store::create($validated);

        return response()->json([
            "error" => false,
            "message" => "success"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $store = Store::find($id);

        if (!$store) {
            return response()->json([
                "error" => true,
                'message' => 'Store not found'
            ]);
        }

        return response()->json([
            "error" => false,
            "message" => "Store fetched successfully",
            "store" => $store
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'phone' => 'string|max:255',
            'address' => 'string|max:255',
            'link_map' => 'string',
            'price' => 'numeric',
        ]);

        $store->update($validated);

        return response()->json([
            "error" => false,
            "message" => "Store updated successfully",
            "store" => $store
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $store = Store::find($id);

        if (!$store) {
            return response()->json([
                "error" => true,
                'message' => 'Store not found'
            ]);
        }

        $store->delete();

        return response()->json([
            "error" => false,
            'message' => 'Store deleted successfully'
        ]);
    }

    public function search(Request $request)
    {
        // Validasi input query
        $validated = $request->validate([
            'q' => 'required|string|max:255',
        ]);

        // Dapatkan nilai query yang telah divalidasi
        $query = $validated['q'];

        $stores = Store::search($query);

        return response()->json([
            "error" => false,
            "message" => "Stores fetched successfully",
            "listStore" => $stores
        ]);
    }
}
