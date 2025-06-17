<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $reviews = Review::all();
            return response()->json([
                'status' => 'success',
                'data' => $reviews
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch reviews: ' . $e->getMessage()
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
                'product_id' => 'required|integer|exists:products,id',
                'order_item_id' => 'required|integer',
                'rating' => 'required|integer|min:0|max:10',
                'comment' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = 1;

            // Check for duplicate order_item_id with is_approved = 1
            $existingReview = Review::where('id_orderItems', $request->order_item_id)
                ->where('is_approved', 1)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Review for this order item already exists and is approved, duplicate not allowed'
                ], 409); // 409 Conflict for duplicate resource
            }

            $review = Review::create([
                'user_id' => $userId,
                'product_id' => $request->product_id,
                'id_orderItems' => $request->order_item_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_approved' => $request->is_approved ?? 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Review created successfully',
                'data' => $review
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $review = Review::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $review
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Review not found'
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
                'user_id' => 'sometimes|integer|exists:users,id',
                'product_id' => 'sometimes|integer|exists:products,id',
                'id_orderitems' => 'sometimes|integer|exists:orderitems,id',
                'rating' => 'sometimes|integer|min:0|max:10',
                'comment' => 'nullable|string',
                'is_approved' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $review = Review::findOrFail($id);
            $review->update([
                'user_id' => $request->user_id ?? $review->user_id,
                'product_id' => $request->product_id ?? $review->product_id,
                'id_orderitems' => $request->id_orderitems ?? $review->id_orderitems,
                'rating' => $request->rating ?? $review->rating,
                'comment' => $request->comment ?? $review->comment,
                'is_approved' => $request->is_approved ?? $review->is_approved,
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Review updated successfully',
                'data' => $review
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Review deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete review: ' . $e->getMessage()
            ], 500);
        }
    }
}