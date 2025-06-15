<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // First drop the existing payment_method column
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        // Then add all the new columns
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('snap_token')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_payment_type')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->enum('payment_method', ['credit_card', 'bank_transfer', 'gopay', 'shopeepay', 'qris'])->default('credit_card');
        });
    }

    public function down()
    {
        // First drop all the new columns
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'snap_token',
                'midtrans_transaction_id',
                'midtrans_payment_type',
                'payment_status',
                'payment_method'
            ]);
        });

        // Then restore the original payment_method
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('payment_method', ['transfer_bank'])->default('transfer_bank');
        });
    }
};