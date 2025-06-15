<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->date('reservation_date');
            $table->time('reservation_time');
            $table->integer('people_count');
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_method', ['transfer_bank'])->default('transfer_bank');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->string('reservation_number')->unique();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};