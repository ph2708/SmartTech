<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Aparelho Pronto para Retirada</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f4f7f6; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .header { background: #1a1a2e; color: #ffffff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; color: #e63946; }
        .body { padding: 28px 24px; line-height: 1.6; font-size: 15px; }
        .box-info { background: #f8fafc; border-left: 4px solid #10b981; padding: 16px; border-radius: 6px; margin: 20px 0; }
        .footer { background: #f1f5f9; color: #64748b; font-size: 12px; text-align: center; padding: 16px; border-top: 1px solid #e2e8f0; }
        .btn { display: inline-block; background: #25D366; color: #ffffff !important; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ {{ $storeName }}</h1>
            <p style="margin: 6px 0 0 0; font-size: 14px; opacity: 0.8;">Assistência Técnica & Acessórios</p>
        </div>
        <div class="body">
            <h2 style="color: #10b981; margin-top: 0;">🎉 Olá, {{ $order->customer_name }}!</h2>
            <p>Temos uma ótima notícia: o reparo do seu equipamento foi concluído com sucesso e ele já se encontra <strong>PRONTO PARA RETIRADA</strong> na nossa loja!</p>

            <div class="box-info">
                <p style="margin: 4px 0;"><strong>Ordem de Serviço:</strong> #{{ $order->os_number }}</p>
                <p style="margin: 4px 0;"><strong>Aparelho:</strong> {{ ucfirst($order->device_type) }} {{ $order->device_brand }} {{ $order->device_model }}</p>
                @if($order->services_description)<p style="margin: 4px 0;"><strong>Serviços Executados:</strong> {{ $order->services_description }}</p>@endif
                <p style="margin: 4px 0; font-size: 16px; color: #0f172a;"><strong>Valor Final:</strong> <span style="color: #10b981; font-weight: bold;">{{ $order->formatted_final_amount }}</span></p>
            </div>

            <p>Você já pode comparecer ao nosso endereço para retirar seu aparelho e realizar o pagamento.</p>

            @if($tenant && $tenant->address)
            <p style="font-size: 13px; color: #64748b;">📍 <strong>Endereço:</strong> {{ $tenant->address }}{{ $tenant->city ? ', ' . $tenant->city : '' }}{{ $tenant->state ? ' - ' . $tenant->state : '' }}</p>
            @endif

            @if($tenant && $tenant->whatsapp_link)
            <div style="text-align: center;">
                <a href="{{ $tenant->whatsapp_link }}" class="btn">Falar no WhatsApp</a>
            </div>
            @endif
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} {{ $storeName }}. Todos os direitos reservados.<br>Garantia legal de 90 dias nos serviços prestados.</p>
        </div>
    </div>
</body>
</html>
