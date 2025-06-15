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
        Schema::table('products', function (Blueprint $table) {
            // Periksa apakah kolom sudah ada sebelum menambahkannya
            if (!Schema::hasColumn('products', 'is_promo')) {
                $table->boolean('is_promo')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('products', 'promo_price')) {
                $table->decimal('promo_price', 10, 2)->nullable()->after('is_promo');
            }
            
            if (!Schema::hasColumn('products', 'promo_start')) {
                $table->dateTime('promo_start')->nullable()->after('promo_price');
            }
            
            if (!Schema::hasColumn('products', 'promo_end')) {
                $table->dateTime('promo_end')->nullable()->after('promo_start');
            }
            
            if (!Schema::hasColumn('products', 'promo_description')) {
                $table->text('promo_description')->nullable()->after('promo_end');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Periksa apakah kolom ada sebelum menghapusnya
            $columns = [
                'is_promo',
                'promo_price',
                'promo_start',
                'promo_end',
                'promo_description'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};