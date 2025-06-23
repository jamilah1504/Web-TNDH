<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     try {
    //         $reviews = Review::all();
    //         return response()->json([
    //             'status' => 'success',
    //             'data' => $reviews
    //         ], 200);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to fetch reviews: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function index(Request $request) // Tambahkan Request $request
{
    try {
        // Mulai query builder
        $query = Review::query();

        // Tambahkan eager loading untuk relasi user
        // Ini akan mengambil data user yang membuat review secara efisien
        $query->with('user:id,name,photo_url'); // Ambil hanya kolom yg dibutuhkan

        // Cek jika ada parameter 'product_id' di request
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Cek jika ada parameter 'is_approved' di request
        if ($request->has('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        // Urutkan berdasarkan yang terbaru
        $reviews = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $reviews
        ], 200);

    } catch (Exception $e) {
        report($e); // Laporkan error untuk debugging
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch reviews: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Store a newly created resource in storage.
     */
    // app/Http/Controllers/ReviewController.php

public function store(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'user_id' => 'required|integer|exists:users,id',
            'id_orderItems' => 'required|integer|exists:order_items,id', // Pastikan exists di tabel yang benar
            'rating' => 'required|integer|min:1|max:5', // Rating biasanya 1-5
            'comment' => 'nullable|string' // Komentar bisa jadi opsional
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Gunakan updateOrCreate untuk membuat review baru atau update jika sudah ada
        // Kriteria pencarian: kombinasi user_id dan id_orderItems harus unik
        $review = Review::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'id_orderItems' => $request->id_orderItems
            ],
            [
                'product_id' => $request->product_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_approved' => 1, // Default ke 1 atau sesuai logika bisnis Anda
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Review submitted successfully', // Pesan yang lebih umum
            'data' => $review
        ], 201); // 201 Created

    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to submit review: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
    // app/Http/Controllers/Api/ReviewController.php


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