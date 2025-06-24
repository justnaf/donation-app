<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;
use Inertia\Inertia;

class DonationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:donation_programs,id',
            'amount' => 'required|numeric|min:10000',
            'donator_name' => 'required|string|max:255',
            'donator_email' => 'required|email|max:255',
            'payment_method' => ['required', 'string', Rule::in(array_keys(config('fees.midtrans')))],
            'message' => 'nullable|string',
            'is_anonymous' => 'nullable|boolean',
        ]);

        $donationAmount = (float) $request->amount;
        $paymentMethod = $request->payment_method;

        $feeRule = config('fees.midtrans.' . $paymentMethod, 0);

        // 3. Hitung biaya transaksi menggunakan helper
        $fee = $this->calculateFee($donationAmount, $feeRule);

        // 4. Hitung total yang harus dibayar
        $totalAmount = $donationAmount + $fee;

        // 5. Buat record donasi dengan rincian amount dan fee
        $donation = Donation::create([
            'order_id' => 'DONA-' . uniqid(),
            'donation_program_id' => $request->program_id,
            'user_id' => Auth::id(),
            'donator_name' => $request->donator_name,
            'donator_email' => $request->donator_email,
            'amount' => $donationAmount, // Jumlah donasi murni
            'fee' => $fee,               // Biaya transaksi yang sudah dihitung
            'status' => 'pending',
            'payment_method' => $paymentMethod,
            'message' => $request->message,
            'is_anonymous' => $request->is_anonymous ?? false,
        ]);

        // 6. Atur konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');

        // 7. Siapkan parameter untuk Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $donation->order_id,
                'gross_amount' => $totalAmount, // Kirim TOTAL AMOUNT ke Midtrans
            ],
            'customer_details' => [
                'first_name' => $donation->donator_name,
                'email' => $donation->donator_email,
            ],
            'item_details' => [ // Rincian item untuk transparansi
                [
                    'id' => 'DONASI-' . $donation->program->id,
                    'price' => $donationAmount,
                    'quantity' => 1,
                    'name' => 'Donasi: ' . $donation->program->name,
                ],
                [
                    'id' => 'TRANSACTION_FEE',
                    'price' => $fee,
                    'quantity' => 1,
                    'name' => 'Biaya Transaksi',
                ],
            ],
            "enabled_payments" => [(string) $paymentMethod],
        ];

        try {
            $transaction = Snap::createTransaction($params);
            $redirectUrl = $transaction->redirect_url;
            return response()->json([
                'sent_params' => $params,
                'order_id' => $donation->order_id,
                'redirect_url' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error dari Midtrans',
                'error' => $e->getMessage() // Kirim pesan error sebenarnya
            ], 500);
        }
    }

    /**
     * Helper method untuk menghitung biaya transaksi.
     */
    private function calculateFee(float $amount, $feeRule): float
    {
        if (is_numeric($feeRule)) {
            return (float) $feeRule;
        }

        if (is_string($feeRule) && str_contains($feeRule, '%')) {
            $percentage = (float) str_replace('%', '', $feeRule);
            return ceil(($amount * $percentage) / 100);
        }

        return 0;
    }

    /**
     * Menampilkan halaman status/tunggu.
     */
    public function show(Donation $donation)
    {
        return Inertia::render('Public/Programs/Status', [
            'donation' => $donation->load('program'), // Kirim data donasi ke view
        ]);
    }

    /**
     * Endpoint API untuk memeriksa status terakhir dari sebuah donasi.
     */
    public function checkStatus(Donation $donation)
    {
        return response()->json([
            'status' => $donation->status,
        ]);
    }
}
