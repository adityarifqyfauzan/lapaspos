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
        'lunas' => 2,
        'dibatalkan' => 3,
    ],

    'payment_status' => [
        'pending' => 1,
        'sukses' => 2,
        'gagal' => 3
    ],

    'payment_method' => [
        'tunai' => 'tunai'
    ],

    'activity' => [
        'category' => 'kategori',
        'item_unit' => 'satuan',
        'order' => 'pesanan',
        'outlet' => 'outlet',
        'payment' => 'pembayaran',
        'product' => 'produk',
        'reporting' => 'laporan',
        'role' => 'role',
        'stock_in' => 'pembelian',
        'supplier' => 'pemasok',
        'user' => 'pengguna'
    ],

    'activity_purpose' => [
        'create' => 'create',
        'update' => 'update',
        'delete' => 'delete'
    ],

    'stock_status' => [
        'in' => 'in',
        'out' => 'out',
        'sale' => 'sale',
        'return' => 'return',
        'opname' => 'opname',
        'order_cancel' => 'order_cancel'
    ]
];
