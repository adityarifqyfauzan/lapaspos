<?php

return [
    'code' => [
        'transaction' => 'TRX',
        'invoice' => 'INV',
        'product' => 'PROD'
    ],

    'roles' => [
        'admin' => 1,
        'cashier' => 2,
    ],

    'order_status' => [
        'belum-dibayar' => 1,
        'sukses' => 2,
        'dibatalkan' => 3,
    ],

    'payment_status' => [
        'pending' => 1,
        'sukses' => 2,
        'gagal' => 3
    ],

    'payment_method' => [
        'tunai' => 'tunai'
    ]
];
