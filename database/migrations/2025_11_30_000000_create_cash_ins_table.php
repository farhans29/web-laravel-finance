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
        Schema::create('cash_ins', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no')->unique();
            $table->string('pks_no');
            $table->enum('category', ['internal', 'external']);
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('partner_name');
            $table->string('faculty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_ins');
    }
};
