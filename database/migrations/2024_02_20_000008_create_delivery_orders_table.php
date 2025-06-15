<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->text('shipping_address');
            $table->text('delivery_notes')->nullable();
            $table->string('origin_city')->nullable();
            $table->string('destination_city')->nullable();
            $table->string('courier')->nullable();
            $table->string('service')->nullable();
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_orders');
    }
};