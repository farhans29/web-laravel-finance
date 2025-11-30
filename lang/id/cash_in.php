<?php

return [
    'title' => 'Kas Masuk',
    'title_plural' => 'Kas Masuk',
    'navigation_label' => 'Kas Masuk',

    'fields' => [
        'receipt_no' => 'No. Kwitansi',
        'pks_no' => 'No. PKS',
        'category' => 'Kategori',
        'amount' => 'Jumlah',
        'date' => 'Tanggal',
        'partner_name' => 'Nama Mitra',
        'faculty' => 'Fakultas',
        'cash_in_status' => 'Status Kas Masuk',
        'created_at' => 'Dibuat Pada',
    ],

    'category' => [
        'internal' => 'Internal',
        'external' => 'Eksternal',
    ],

    'status' => [
        'approved' => 'Disetujui',
        'not_approved' => 'Belum Disetujui',
        'rejected' => 'Ditolak',
    ],

    'filters' => [
        'date_from' => 'Tanggal Dari',
        'date_until' => 'Tanggal Sampai',
    ],

    'actions' => [
        'create' => 'Buat Kas Masuk Baru',
        'edit' => 'Edit Kas Masuk',
        'view' => 'Lihat Kas Masuk',
        'delete' => 'Hapus Kas Masuk',
    ],

    'messages' => [
        'created' => 'Kas Masuk berhasil dibuat',
        'updated' => 'Kas Masuk berhasil diperbarui',
        'deleted' => 'Kas Masuk berhasil dihapus',
    ],
];
