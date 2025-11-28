<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'name',
        'partner',
        'activity_name',
        'virtual_account_no',
        'bill',
        'invoice_status',
    ];

    protected $casts = [
        'bill' => 'decimal:2',
    ];
}
