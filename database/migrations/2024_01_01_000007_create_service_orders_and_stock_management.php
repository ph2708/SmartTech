<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adicionar controle de estoque e tipo (produto físico vs serviço) na tabela products
        Schema::table('products', function (Blueprint $table) {
            $table->enum('type', ['product', 'service'])->default('product')->after('category_id');
            $table->integer('stock_quantity')->default(0)->after('price');
            $table->boolean('manage_stock')->default(true)->after('stock_quantity');
            $table->integer('min_stock_alert')->default(2)->after('manage_stock');
        });

        // 2. Tabela de Ordens de Serviço (OS) para Assistência Técnica
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('os_number')->index(); // Ex: OS-2026-0001
            
            // Dados do Cliente
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_document')->nullable(); // CPF
            
            // Dados do Equipamento
            $table->enum('device_type', ['celular', 'computador', 'notebook', 'tablet', 'outro'])->default('celular');
            $table->string('device_brand'); // Apple, Samsung, Dell, etc.
            $table->string('device_model'); // iPhone 13, Galaxy S23, Inspiron 15, etc.
            $table->string('device_serial')->nullable(); // IMEI ou Número de Série
            $table->string('device_password')->nullable(); // Senha de desbloqueio / padrão
            $table->text('device_condition')->nullable(); // Marcas de uso, tela trincada, etc.
            $table->text('device_accessories')->nullable(); // Acompanha carregador, chip, capinha, etc.
            
            // Diagnóstico & Serviços
            $table->text('reported_defect'); // Defeito relatado pelo cliente
            $table->text('technical_diagnosis')->nullable(); // Diagnóstico técnico / laudo
            $table->text('services_description')->nullable(); // Serviços a realizar
            
            // Valores & Prazos
            $table->decimal('parts_cost', 10, 2)->default(0); // Custo das peças
            $table->decimal('labor_cost', 10, 2)->default(0); // Valor da mão de obra
            $table->decimal('total_amount', 10, 2)->default(0); // Total da OS
            $table->decimal('discount_amount', 10, 2)->default(0); // Desconto
            $table->decimal('final_amount', 10, 2)->default(0); // Valor final
            $table->enum('payment_method', ['pix', 'cartao_credito', 'cartao_debito', 'dinheiro', 'outro', 'aguardando'])->default('aguardando');
            $table->date('entry_date');
            $table->date('estimated_date')->nullable();
            $table->date('completion_date')->nullable();
            
            // Status do Fluxo da Assistência
            $table->enum('status', [
                'orcamento',          // Em Orçamento
                'aguardando_peca',    // Aguardando Peça
                'aprovado',           // Aprovado / Em Reparo
                'pronto',             // Pronto para Retirada
                'entregue',           // Entregue / Finalizado
                'cancelado'           // Cancelado / Sem Reparo
            ])->default('orcamento');
            
            $table->text('internal_notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'os_number']);
            $table->index(['tenant_id', 'status', 'entry_date']);
            $table->index(['tenant_id', 'device_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_orders');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['type', 'stock_quantity', 'manage_stock', 'min_stock_alert']);
        });
    }
};
