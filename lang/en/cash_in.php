<?php

return [
    'title' => 'Cash In',
    'title_plural' => 'Cash Ins',
    'navigation_label' => 'Cash In',

    'fields' => [
        'receipt_no' => 'Receipt No.',
        'pks_no' => 'PKS No.',
        'category' => 'Category',
        'amount' => 'Amount',
        'date' => 'Date',
        'partner_name' => 'Partner Name',
        'faculty' => 'Faculty',
        'cash_in_status' => 'Cash In Status',
        'created_at' => 'Created At',
    ],

    'category' => [
        'internal' => 'Internal',
        'external' => 'External',
    ],

    'status' => [
        'approved' => 'Approved',
        'not_approved' => 'Not Approved',
        'rejected' => 'Rejected',
    ],

    'filters' => [
        'date_from' => 'Date From',
        'date_until' => 'Date Until',
    ],

    'actions' => [
        'create' => 'Create New Cash In',
        'edit' => 'Edit Cash In',
        'view' => 'View Cash In',
        'delete' => 'Delete Cash In',
    ],

    'messages' => [
        'created' => 'Cash In created successfully',
        'updated' => 'Cash In updated successfully',
        'deleted' => 'Cash In deleted successfully',
    ],
];
