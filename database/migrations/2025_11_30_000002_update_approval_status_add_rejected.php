<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update cash_ins table
        DB::statement("ALTER TABLE cash_ins MODIFY COLUMN approval_status ENUM('approved', 'not_approved', 'rejected') DEFAULT 'not_approved'");

        // Update invoices table
        DB::statement("ALTER TABLE invoices MODIFY COLUMN invoice_status ENUM('approved', 'not_approved', 'rejected') DEFAULT 'not_approved'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert cash_ins table
        DB::statement("ALTER TABLE cash_ins MODIFY COLUMN approval_status ENUM('approved', 'not_approved') DEFAULT 'not_approved'");

        // Revert invoices table
        DB::statement("ALTER TABLE invoices MODIFY COLUMN invoice_status ENUM('approved', 'not_approved') DEFAULT 'not_approved'");
    }
};
