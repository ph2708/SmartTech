<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Suporte a Filiais / Matriz
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->boolean('is_branch')->default(false)->after('parent_id');
            $table->string('branch_name')->nullable()->after('is_branch'); // Ex: "Filial Centro", "Filial Shopping"
        });

        // Tabela de Configurações Globais do Sistema (para alternar modo SaaS vs Loja Única pelo painel)
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_branch', 'branch_name']);
        });
    }
};
