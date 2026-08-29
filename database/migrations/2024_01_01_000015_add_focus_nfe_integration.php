<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Configurações de Emissão Fiscal (Focus NFe) na tabela tenants
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('nfe_enabled')->default(false);
            $table->enum('nfe_environment', ['homologacao', 'producao'])->default('homologacao');
            $table->string('nfe_token')->nullable(); // Token Focus NFe
            $table->string('cnpj')->nullable();
            $table->string('inscricao_estadual')->nullable();
            $table->string('inscricao_municipal')->nullable();
            $table->string('regime_tributario')->nullable()->default('1'); // 1 = Simples Nacional
        });

        // 2. Tabela de Notas Fiscais emitidas (NF-e de Produtos / NFC-e / NFS-e de Serviços)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('service_order_id')->nullable()->constrained('service_orders')->nullOnDelete();
            $table->string('reference_code')->unique(); // Referência única para a Focus NFe
            $table->enum('type', ['nfe', 'nfce', 'nfse'])->default('nfce');
            $table->enum('status', ['processando', 'autorizado', 'erro_autorizacao', 'cancelado'])->default('processando');
            $table->decimal('total_amount', 10, 2);
            $table->string('customer_name')->nullable();
            $table->string('customer_cpf_cnpj')->nullable();
            $table->string('access_key')->nullable(); // Chave de acesso de 44 dígitos
            $table->string('nfe_number')->nullable();
            $table->string('nfe_series')->nullable();
            $table->string('pdf_url')->nullable(); // Link para DANFE
            $table->string('xml_url')->nullable(); // Link para XML
            $table->text('error_message')->nullable();
            $table->json('payload_sent')->nullable();
            $table->json('response_received')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'reference_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'nfe_enabled', 'nfe_environment', 'nfe_token', 'cnpj',
                'inscricao_estadual', 'inscricao_municipal', 'regime_tributario'
            ]);
        });
    }
};
