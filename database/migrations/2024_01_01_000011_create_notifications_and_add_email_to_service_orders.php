<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adiciona customer_email na tabela de ordens de serviço
        Schema::table('service_orders', function (Blueprint $table) {
            $table->string('customer_email')->nullable()->after('customer_phone');
        });

        // 2. Tabela de Log e Fila de Notificações (E-mail e SMS)
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_order_id')->nullable()->constrained('service_orders')->nullOnDelete();
            $table->enum('channel', ['email', 'sms', 'whatsapp'])->default('email');
            $table->string('recipient'); // e-mail ou número de telefone
            $table->string('subject')->nullable();
            $table->text('message');
            $table->enum('status', ['sent', 'pending', 'failed'])->default('sent');
            $table->string('provider')->default('smtp'); // smtp, twilio, zenvia, totalvoice, etc.
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'channel', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropColumn('customer_email');
        });
    }
};
