<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Registro de Visitas e Cliques de WhatsApp (Cliques de Entrada)
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['page_view', 'whatsapp_click'])->default('page_view');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->index(['tenant_id', 'type', 'date']);
            $table->index(['tenant_id', 'product_id']);
        });

        // Registro de Controle de Vendas / Pedidos
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['pix', 'cartao_credito', 'cartao_debito', 'dinheiro', 'outro'])->default('pix');
            $table->enum('status', ['pendente', 'concluido', 'cancelado'])->default('concluido');
            $table->text('notes')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->index(['tenant_id', 'date']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('analytics_events');
    }
};
