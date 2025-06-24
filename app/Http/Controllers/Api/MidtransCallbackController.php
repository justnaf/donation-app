<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransCallbackController extends Controller
{
    /**
     * Handle Midtrans notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        // Atur konfigurasi Midtrans
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$serverKey = config('services.midtrans.server_key');

        // Buat instance notifikasi Midtrans
        try {
            $notification = new Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid notification format.'], 400);
        }

        // Ambil data dari notifikasi
        $orderId = $notification->order_id;
        $statusCode = $notification->status_code;
        $grossAmount = $notification->gross_amount;
        $signatureKey = $notification->signature_key;
        $transactionStatus = $notification->transaction_status;
        $paymentType = $notification->payment_type;

        // Buat signature key untuk verifikasi dari sisi server kita
        $serverSignatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . config('services.midtrans.server_key'));

        // Verifikasi signature key untuk keamanan
        if ($signatureKey !== $serverSignatureKey) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Temukan donasi berdasarkan order_id
        $donation = Donation::where('order_id', $orderId)->first();

        if (!$donation) {
            return response()->json(['message' => 'Donation not found'], 404);
        }

        // Jangan proses lagi jika statusnya sudah bukan 'pending' (mencegah duplikasi)
        if ($donation->status !== 'pending') {
            return response()->json(['message' => 'This order has already been processed.'], 200);
        }

        // Update status donasi berdasarkan status transaksi Midtrans
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            $donation->status = 'paid';
        } elseif ($transactionStatus == 'pending') {
            $donation->status = 'pending';
        } else {
            // Untuk 'deny', 'expire', 'cancel', dll.
            $donation->status = 'failed';
        }

        // Simpan juga metode pembayaran dan detail lengkapnya untuk arsip
        $donation->payment_method = $paymentType;
        $donation->payment_details = $notification->getResponse();
        $donation->save();

        return response()->json(['message' => 'Notification successfully processed'], 200);
    }
}
