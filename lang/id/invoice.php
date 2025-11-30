<?php

return [
    'title' => 'Invoice',
    'title_plural' => 'Invoice',
    'navigation_label' => 'Invoice',

    'fields' => [
        'invoice_no' => 'No. Invoice',
        'name' => 'Nama',
        'partner' => 'Mitra',
        'activity_name' => 'Nama Kegiatan',
        'virtual_account_no' => 'Nomor Virtual Account',
        'bill' => 'Tagihan',
        'invoice_status' => 'Status Invoice',
        'created_at' => 'Dibuat Pada',
    ],

    'status' => [
        'approved' => 'Disetujui',
        'not_approved' => 'Belum Disetujui',
        'rejected' => 'Ditolak',
    ],

    'actions' => [
        'create' => 'Buat Invoice Baru',
        'edit' => 'Edit Invoice',
        'view' => 'Lihat Invoice',
        'delete' => 'Hapus Invoice',
    ],

    'messages' => [
        'created' => 'Invoice berhasil dibuat',
        'updated' => 'Invoice berhasil diperbarui',
        'deleted' => 'Invoice berhasil dihapus',
    ],
];