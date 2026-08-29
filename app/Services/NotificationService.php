<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\ServiceOrder;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Notifica o cliente quando o equipamento/OS fica pronto para retirada.
     */
    public function notifyOrderReady(ServiceOrder $order): array
    {
        $results = [
            'email' => false,
            'sms' => false,
        ];

        $tenant = $order->tenant ?? Tenant::find($order->tenant_id);
        $storeName = $tenant ? $tenant->name : 'Smart Tech';

        // 1. DISPARO DE E-MAIL
        if (!empty($order->customer_email)) {
            try {
                $subject = "🎉 Seu aparelho está PRONTO para retirada! - {$storeName} (OS #{$order->os_number})";

                // Se a loja cadastrou SMTP próprio, aplica dinamicamente na hora do envio
                if (!empty($tenant->mail_host) && !empty($tenant->mail_username)) {
                    config([
                        'mail.default' => 'smtp',
                        'mail.mailers.smtp.host' => $tenant->mail_host,
                        'mail.mailers.smtp.port' => $tenant->mail_port ?? 587,
                        'mail.mailers.smtp.username' => $tenant->mail_username,
                        'mail.mailers.smtp.password' => $tenant->mail_password,
                        'mail.mailers.smtp.encryption' => ($tenant->mail_encryption === 'none' ? null : $tenant->mail_encryption),
                        'mail.from.address' => $tenant->mail_from_address ?: config('mail.from.address'),
                        'mail.from.name' => $tenant->mail_from_name ?: $storeName,
                    ]);
                }

                Mail::send('emails.order_ready', [
                    'order' => $order,
                    'tenant' => $tenant,
                    'storeName' => $storeName,
                ], function ($message) use ($order, $subject, $storeName, $tenant) {
                    $fromAddress = $tenant->mail_from_address ?: config('mail.from.address');
                    $fromName = $tenant->mail_from_name ?: $storeName;

                    $message->to($order->customer_email, $order->customer_name)
                            ->from($fromAddress, $fromName)
                            ->subject($subject);
                });

                NotificationLog::create([
                    'tenant_id' => $order->tenant_id,
                    'service_order_id' => $order->id,
                    'channel' => 'email',
                    'recipient' => $order->customer_email,
                    'subject' => $subject,
                    'message' => "E-mail de conclusão enviado com sucesso para {$order->customer_name}.",
                    'status' => 'sent',
                    'provider' => config('mail.default', 'smtp'),
                    'sent_at' => now(),
                ]);

                $results['email'] = true;
            } catch (\Exception $e) {
                Log::error("Erro ao enviar e-mail de OS pronta: " . $e->getMessage());

                NotificationLog::create([
                    'tenant_id' => $order->tenant_id,
                    'service_order_id' => $order->id,
                    'channel' => 'email',
                    'recipient' => $order->customer_email,
                    'subject' => "OS #{$order->os_number} Pronta",
                    'message' => 'Falha no envio de e-mail.',
                    'status' => 'failed',
                    'provider' => config('mail.default', 'smtp'),
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        // 2. DISPARO / DRIVER DE SMS
        $smsProvider = $tenant->sms_provider ?: config('services.sms.provider', 'log');
        $smsEnabled = $tenant->sms_enabled ?? config('services.sms.enabled', false);

        if (!empty($order->customer_phone)) {
            $smsMessage = "Olá {$order->customer_name}! Seu {$order->device_brand} {$order->device_model} (OS #{$order->os_number}) está PRONTO para retirada na {$storeName}. Valor: {$order->formatted_final_amount}.";

            $smsStatus = $this->sendSms($order->customer_phone, $smsMessage, $smsProvider, $smsEnabled, $tenant);

            NotificationLog::create([
                'tenant_id' => $order->tenant_id,
                'service_order_id' => $order->id,
                'channel' => 'sms',
                'recipient' => $order->customer_phone,
                'message' => $smsMessage,
                'status' => $smsStatus['status'],
                'provider' => $smsProvider,
                'error_message' => $smsStatus['error'] ?? null,
                'sent_at' => $smsStatus['status'] === 'sent' ? now() : null,
            ]);

            $results['sms'] = $smsStatus['status'] === 'sent';
        }

        return $results;
    }

    /**
     * Driver centralizador de SMS
     */
    protected function sendSms(string $phone, string $message, string $provider, bool $enabled): array
    {
        // Se o gateway de SMS não estiver ativado ou com chave de API, registra em log e deixa pronto
        if (!$enabled) {
            Log::info("[SMS SIMULATION] Para: {$phone} | Mensagem: {$message}");
            return ['status' => 'pending', 'error' => 'Gateway SMS em modo simulação (Configure API Key no .env)'];
        }

        try {
            switch ($provider) {
                case 'twilio':
                    // $sid = config('services.twilio.sid');
                    // $token = config('services.twilio.token');
                    // Twilio Client implementation
                    break;

                case 'zenvia':
                    // Zenvia SMS API REST implementation
                    break;

                case 'totalvoice':
                    // TotalVoice / Zenvia Voz e SMS
                    break;

                default:
                    Log::info("[SMS LOG] Para {$phone}: {$message}");
                    break;
            }

            return ['status' => 'sent'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }
}
