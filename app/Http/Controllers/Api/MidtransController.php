<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // <-- Import DB Facade untuk transaksi
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Models\Order; // <-- Import model Order
use App\Models\OrderItem; // <-- Import model OrderItem
use App\Models\Payment;

class MidtransController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans tidak berubah
        Config::$serverKey    = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized  = config('services.midtrans.isSanitized');
        Config::$is3ds        = config('services.midtrans.is3ds');
    }

    public function createTransaction(Request $request)
    {
        // Validasi data tidak berubah
        $validator = Validator::make($request->all(), [
            'order_summary.total_amount' => 'required|numeric',
            'customer.user_id' => 'required|integer|exists:users,id', // Pastikan user ada
            'customer.name' => 'required|string',
            'customer.email' => 'required|email',
            'customer.phone' => 'required|string',
            'customer.address' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.product_name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();
        
        // ---- LOGIKA DATABASE DIMULAI DI SINI ----
        $orderId = 'ORDER-' . uniqid();

        // Gunakan DB Transaction untuk memastikan semua data konsisten
        DB::beginTransaction();

        try {
            // 1. Buat pesanan (Order) di database
            $order = Order::create([
                'id' => $orderId,
                'user_id' => $validatedData['customer']['user_id'],
                'total_amount' => $validatedData['order_summary']['total_amount'],
                'status' => 'pending', // <-- Status awal
            ]);

            Payment::create([
                'order_id' => $orderId,
                'user_id' => $validatedData['customer']['user_id'],
                'amount' => $validatedData['order_summary']['total_amount'],
                'status' => 'pending', // Always pending initially
                'payment_date' => now(),
            ]);

            // 2. Simpan setiap item pesanan ke tabel order_items
            foreach ($validatedData['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // ---- PERSIAPAN DATA UNTUK MIDTRANS ----
            $item_details = [];
            foreach ($validatedData['items'] as $item) {
                $item_details[] = [
                    'id'       => $item['product_id'],
                    'price'    => $item['price'],
                    'quantity' => $item['quantity'],
                    'name'     => $item['product_name'],
                ];
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $validatedData['order_summary']['total_amount'],
                ],
                'customer_details' => [
                    'first_name' => $validatedData['customer']['name'],
                    'email'      => $validatedData['customer']['email'],
                    'phone'      => $validatedData['customer']['phone'],
                    'address'    => $validatedData['customer']['address'],
                ],
                'item_details' => $item_details,
            ];
            
            // Dapatkan Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            // Jika semua berhasil, commit transaksi database
            DB::commit();
            
            // Kirim token sebagai response
            return response()->json(['snap_token' => $snapToken]);

        } catch (\Exception $e) {
            // Jika terjadi error, batalkan semua query database
            DB::rollBack();
            
            \Log::error('Payment Creation Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memproses pesanan. Silakan coba lagi.'], 500);
        }
    }

    public function notificationHandler(Request $request)
    {
        try {
            $notif = new Notification();
            
            $transactionStatus = $notif->transaction_status;
            $orderId = $notif->order_id;
            $fraudStatus = $notif->fraud_status;

            // 1. Cari pesanan di database berdasarkan ID
            $order = Order::find($orderId);
            $payment = Payment::where('order_id', $orderId);
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            

            // 2. Lakukan verifikasi signature key (SANGAT PENTING UNTUK KEAMANAN)
            $signatureKey = hash('sha512', $orderId . $notif->status_code . $notif->gross_amount . config('services.midtrans.serverKey'));
            if ($notif->signature_key != $signatureKey) {
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            // 3. Update status pesanan berdasarkan notifikasi dari Midtrans
            // Kita akan langsung menggunakan status dari Midtrans sesuai permintaan.
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    // TODO: Set your transaction status to 'challenge'
                    $order->update(['status' => 'Diproses']);
                    $payment->update(['status' => 'complated']);
                } else if ($fraudStatus == 'accept') {
                    // TODO: Set your transaction status to 'success'
                    $order->update(['status' => 'Diproses']);
                    $payment->update(['status' => 'complated']);
                }
            } else if ($transactionStatus == 'settlement') {
                // TODO: Set your transaction status to 'settlement' (pembayaran berhasil)
                $order->update(['status' => 'Diproses']);
                $payment->update(['status' => 'complated']);

                // Di sini Anda bisa menambahkan logika lain, seperti mengurangi stok barang
            } else if ($transactionStatus == 'pending') {
                // TODO: Set your transaction status to 'pending'
                $order->update(['status' => 'pending']);
            } else if ($transactionStatus == 'deny') {
                // TODO: Set your transaction status to 'denied'
                $order->update(['status' => 'failed']);
                $payment->update(['status' => 'failed']);

            } else if ($transactionStatus == 'expire') {
                // TODO: Set your transaction status to 'expired'
                $order->update(['status' => 'failed']);
            } else if ($transactionStatus == 'cancel') {
                // TODO: Set your transaction status to 'cancelled'
                $order->update(['status' => 'failed']);
                $payment->update(['status' => 'failed']);
            }

            return response()->json(['message' => 'Notification handled successfully'], 200);

        } catch (\Exception $e) {
            \Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
    public function updateStatusFromClient(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|string|exists:orders,id',
                'transaction_status' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            try {
                // Cari pesanan berdasarkan order_id
                $order = Order::find($request->order_id);
                
                
                // Cek untuk menghindari menimpa status yang sudah final dari webhook
                // Contoh: Jika status sudah 'settlement', jangan diubah lagi jadi 'pending'
                if ($order->status === 'settlement' || $order->status === 'capture') {
                    return response()->json(['message' => 'Order status is final and cannot be changed from client.'], 409); // 409 Conflict
                }
                
                // Update status pesanan
                $order->update([
                    'status' => $request->transaction_status
                ]);

                return response()->json([
                    'message' => 'Order status and payment updated successfully for quick feedback.',
                    'data' => [
                        'order' => $order,
                        'payment' => $payment
                    ]
                ]);

            } catch (\Exception $e) {
                \Log::error('Client Status Update Error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to update order status or create payment.'], 500);
            }
        }
}