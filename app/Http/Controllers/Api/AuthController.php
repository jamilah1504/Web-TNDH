<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
     public function index(User $user)
    {
        $users = User::where('id',$user->id)->get();
        return response()->json($users);
    }
    
     public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user
                ]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to register user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log in a user.
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to log in: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log out a user.
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User logged out successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to log out: ' . $e->getMessage()
            ], 500);
        }
    }

    public function alamat(Request $request, string $id)
    {
        // Langkah 1: Validasi data yang masuk dari request
        $validator = Validator::make($request->all(), [
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:100',
            'name'  => 'nullable|string|max:100',
            'email' => 'nullable|email',
        ]);

        // Jika validasi gagal, kembalikan response error
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data yang diberikan tidak valid.',
                'errors'  => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        try {
            // Langkah 2: Cari pengguna berdasarkan ID
            // findOrFail akan otomatis memberikan error 404 jika user tidak ditemukan
            $user = User::findOrFail($id);

            // Langkah 3: Update data pengguna dengan data yang sudah divalidasi
            $user->update($validator->validated());

            // Langkah 4: Kembalikan response sukses
            return response()->json([
                'status'  => 'success',
                'message' => 'Alamat pengguna berhasil diperbarui.',
                'data'    => $user // Opsional: kembalikan data user yang sudah diupdate
            ], 200); // 200 OK

        } catch (Exception $e) {
            // Tangani error lain yang mungkin terjadi (misal: error database)
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui alamat: ' . $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }
}