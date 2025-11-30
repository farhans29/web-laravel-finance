<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cash_ins', function (Blueprint $table) {
            $table->renameColumn('approval_status', 'cash_in_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_ins', function (Blueprint $table) {
            $table->renameColumn('cash_in_status', 'approval_status');
        });
    }
};
