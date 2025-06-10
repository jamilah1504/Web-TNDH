<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment; // Assuming Payment model exists
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Payments = Payment::all();
            return response()->json([
                'status' => 'success',
                'data' => $Payments
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch Payments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $Payment = Payment::create($request->only([
                'name',
                'description',
                'price',
                'stock'
            ]));

            return response()->json([
                'status' => 'success',
                'message' => 'Payment created successfully',
                'data' => $Payment
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create Payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $Payment = Payment::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $Payment
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric|min:0',
                'stock' => 'sometimes|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $Payment = Payment::findOrFail($id);
            $Payment->update($request->only([
                'name',
                'description',
                'price',
                'stock'
            ]));

            return response()->json([
                'status' => 'success',
                'message' => 'Payment updated successfully',
                'data' => $Payment
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update Payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $Payment = Payment::findOrFail($id);
            $Payment->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete Payment: ' . $e->getMessage()
            ], 500);
        }
    }
}