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
            if (!Schema::hasColumn('products', 'promo_banner')) {
                $table->string('promo_banner')->nullable()->after('promo_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'promo_banner')) {
                $table->dropColumn('promo_banner');
            }
        });
    }
};