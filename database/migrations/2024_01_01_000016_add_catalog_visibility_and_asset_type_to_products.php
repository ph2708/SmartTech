<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modificar o enum type de products para suportar 'asset' (Imobilizado / Patrimônio)
        // No MySQL usamos MODIFY COLUMN
        DB::statement("ALTER TABLE products MODIFY COLUMN type ENUM('product', 'service', 'asset') NOT NULL DEFAULT 'product'");

        // 2. Adicionar campos de controle de exibição de catálogo e venda física
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('show_in_catalog')->default(true)->after('is_active');
            $table->boolean('allow_physical_sale')->default(true)->after('show_in_catalog');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['show_in_catalog', 'allow_physical_sale']);
        });
        DB::statement("ALTER TABLE products MODIFY COLUMN type ENUM('product', 'service') NOT NULL DEFAULT 'product'");
    }
};
