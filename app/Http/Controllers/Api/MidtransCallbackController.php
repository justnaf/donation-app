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
        if (!$request->isMethod('post')) {
            abort(404);
        }
        // 1. Atur konfigurasi Midtrans
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$serverKey = config('services.midtrans.server_key');

        // 2. Buat instance notifikasi Midtrans
        try {
            $notification = new Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid notification payload.'], 400);
        }

        // 3. Verifikasi Signature Key (SANGAT PENTING UNTUK KEAMANAN)
        $orderId = $notification->order_id;
        $statusCode = $notification->status_code;
        $grossAmount = $notification->gross_amount;
        $signatureKey = $notification->signature_key;

        // Buat hash dari sisi server kita untuk dicocokkan
        $serverSignatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . config('services.midtrans.server_key'));

        // Jika signature tidak cocok, hentikan proses
        if ($signatureKey !== $serverSignatureKey) {
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        // 4. Temukan donasi di database kita berdasarkan order_id
        $donation = Donation::where('order_id', $orderId)->first();
        if (!$donation) {
            return response()->json(['message' => 'Donation not found.'], 404);
        }

        // 5. Pengecekan Idempotency: Jangan proses notifikasi yang sama berulang kali
        // Jika status donasi sudah bukan 'pending', berarti sudah pernah diproses.
        if ($donation->status !== 'pending') {
            return response()->json(['message' => 'This order has already been processed.'], 200);
        }

        // 6. Update status donasi berdasarkan status transaksi dari Midtrans
        $transactionStatus = $notification->transaction_status;
        $paymentType = $notification->payment_type;

        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                // Pembayaran berhasil (untuk kartu kredit, e-money, VA, dll.)
                $donation->status = 'paid';
                break;
            case 'pending':
                // Pembayaran masih menunggu (misal: transfer bank belum dibayar)
                $donation->status = 'pending';
                break;
            case 'deny':
            case 'expire':
            case 'cancel':
                // Pembayaran gagal, dibatalkan, atau kedaluwarsa
                $donation->status = 'failed';
                break;
        }

        // Simpan juga metode pembayaran dan detail lengkapnya untuk arsip/audit
        $donation->payment_method = $paymentType;
        $donation->payment_details = $notification->getResponse();
        $donation->save();

        // 7. Beri respons 200 OK ke Midtrans
        return response()->json(['message' => 'Notification successfully processed.']);
    }
}
