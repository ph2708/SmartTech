<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Configurações de SMTP / E-mail da Loja
            $table->string('mail_mailer')->default('smtp');
            $table->string('mail_host')->nullable();
            $table->integer('mail_port')->nullable()->default(587);
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('mail_encryption')->nullable()->default('tls');
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->boolean('mail_is_verified')->default(false);
            $table->timestamp('mail_verified_at')->nullable();

            // Configurações de SMS da Loja
            $table->boolean('sms_enabled')->default(false);
            $table->string('sms_provider')->default('log'); // twilio, zenvia, totalvoice, log
            $table->string('sms_api_key')->nullable();
            $table->string('sms_api_secret')->nullable();
            $table->string('sms_from_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password',
                'mail_encryption', 'mail_from_address', 'mail_from_name', 'mail_is_verified', 'mail_verified_at',
                'sms_enabled', 'sms_provider', 'sms_api_key', 'sms_api_secret', 'sms_from_number'
            ]);
        });
    }
};
