<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('shipping_cost');
            $table->text('tracking_info')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn(['status', 'tracking_info']);
        });
    }
};