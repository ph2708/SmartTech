<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela de Transações Financeiras (Fluxo de Caixa Completo: Entradas e Saídas/Despesas)
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['income', 'expense'])->default('income'); // Entrada (Receita) vs Saída (Despesa)
            $table->string('description'); // Descrição da despesa ou entrada
            $table->decimal('amount', 10, 2);
            
            // Categoria Financeira
            $table->string('category')->default('Geral'); 
            // Ex p/ Despesas: Peças/Estoque, Aluguel, Energia/Internet, Salários, Ferramentas, Marketing, Impostos, Outros
            // Ex p/ Entradas: Venda de Balcão, Serviço de Assistência, Acessórios, Perfumes, Outros

            $table->enum('payment_method', ['pix', 'cartao_credito', 'cartao_debito', 'dinheiro', 'boleto', 'transferencia', 'outro'])->default('pix');
            $table->enum('status', ['pago', 'pendente', 'cancelado'])->default('pago');
            
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('service_order_id')->nullable()->constrained('service_orders')->nullOnDelete();
            
            $table->date('date');
            $table->date('due_date')->nullable(); // Data de vencimento p/ despesas a pagar
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'type', 'date']);
            $table->index(['tenant_id', 'category']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
