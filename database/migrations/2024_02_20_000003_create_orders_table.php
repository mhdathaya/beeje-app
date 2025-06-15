<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'delivered', 'canceled'])->default('pending');
            $table->enum('payment_method', ['credit_card', 'bank_transfer', 'gopay', 'shopeepay', 'qris', 'BCA', 'BNI', 'BRI', 'Mandiri'])->nullable();
            $table->enum('order_method', ['delivery', 'reservation'])->nullable(false);
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};