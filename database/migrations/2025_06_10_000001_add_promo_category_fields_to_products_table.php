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
            // Kolom untuk nama promo (seperti "Flash Sale", "Diskon Akhir Pekan", dll)
            if (!Schema::hasColumn('products', 'promo_name')) {
                $table->string('promo_name')->nullable()->after('promo_banner');
            }
            
            // Kolom untuk tipe promo (bisa berupa "category_based", "flash_sale", "special_event", dll)
            if (!Schema::hasColumn('products', 'promo_type')) {
                $table->string('promo_type')->nullable()->after('promo_name');
            }
            
            // Kolom untuk urutan tampilan promo
            if (!Schema::hasColumn('products', 'promo_order')) {
                $table->integer('promo_order')->default(0)->after('promo_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'promo_name',
                'promo_type',
                'promo_order'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};