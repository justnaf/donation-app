<?php
// config/fees.php
return [
    'midtrans' => [
        // Contoh biaya tetap (flat rate) dalam Rupiah
        'shopeepay'             => '2%',
        'other_qris'            => '0.7%',
        'bca_va'                => 4000,
        'bni_va'                => 4000,
        'mandiri_bill'          => 4000,
        'permata_va'            => 4000,

        // Contoh biaya persentase (simpan sebagai string)
        'credit_card'           => '2.9%', // 2.9% dari nominal donasi

    ],
];
