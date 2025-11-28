<?php

return [
    'title' => 'Invoice',
    'title_plural' => 'Invoices',
    'navigation_label' => 'Invoices',

    'fields' => [
        'invoice_no' => 'Invoice No.',
        'name' => 'Name',
        'partner' => 'Partner',
        'activity_name' => 'Activity Name',
        'virtual_account_no' => 'Virtual Account Number',
        'bill' => 'Bill Amount',
        'invoice_status' => 'Invoice Status',
        'created_at' => 'Created At',
    ],

    'status' => [
        'approved' => 'Approved',
        'not_approved' => 'Not Approved',
    ],

    'actions' => [
        'create' => 'Create New Invoice',
        'edit' => 'Edit Invoice',
        'view' => 'View Invoice',
        'delete' => 'Delete Invoice',
    ],

    'messages' => [
        'created' => 'Invoice created successfully',
        'updated' => 'Invoice updated successfully',
        'deleted' => 'Invoice deleted successfully',
    ],
];