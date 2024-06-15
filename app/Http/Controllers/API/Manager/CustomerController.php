<?php

namespace App\Http\Controllers\API\Manager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            "error" => false,
            "message" => "Customers fetched successfully",
            "listCustomer" => Customer::all()
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

        $customer = Customer::create($validated);

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
        $store = Customer::find($id);

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
    public function update(Request $request,$id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'phone' => 'string|max:255',
            'address' => 'string|max:255',
            'link_map' => 'string',
            'price' => 'numeric',
        ]);

        $customer->update($validated);

        return response()->json([
            "error" => false,
            "message" => "Customer updated successfully",
            "customer" => $customer
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                "error" => true,
                'message' => 'Customer not found'
            ]);
        }

        $customer->delete();

        return response()->json([
            "error" => false,
            'message' => 'Customer deleted successfully'
        ]);
    }
}
