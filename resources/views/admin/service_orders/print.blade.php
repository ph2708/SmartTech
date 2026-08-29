<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Comprovante OS #{{ $order->os_number }} - {{ $tenant->name ?? 'SmartTech' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 13px; color: #111; margin: 20px; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { margin: 0 0 4px 0; font-size: 20px; }
        .header p { margin: 2px 0; font-size: 12px; color: #555; }
        .os-title { display: flex; justify-content: space-between; align-items: center; background: #f0f0f0; padding: 8px 12px; border-radius: 4px; margin-bottom: 16px; }
        .section { margin-bottom: 14px; }
        .section-title { font-size: 13px; font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 4px; margin-bottom: 6px; text-transform: uppercase; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table th, table td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #f9f9f9; font-weight: bold; }
        .signatures { margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; text-align: center; }
        .signature-line { border-top: 1px solid #000; padding-top: 6px; font-size: 12px; }
        .terms { font-size: 10px; color: #666; margin-top: 24px; border: 1px dashed #ccc; padding: 8px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; font-weight: bold; background: #e63946; color: white; border: none; border-radius: 6px; cursor: pointer;">🖨️ Imprimir Comprovante</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; background: #eee; border: 1px solid #ccc; border-radius: 6px; cursor: pointer; margin-left: 8px;">Fechar</button>
    </div>

    <div class="header">
        <h1>{{ $tenant->name ?? 'Smart Tech - Assistência Técnica' }}</h1>
        <p>{{ $tenant->description ?? 'Especializada em Celulares, Computadores e Acessórios' }}</p>
        <p>WhatsApp: {{ $tenant->whatsapp ?? '' }} | {{ $tenant->address ?? '' }}</p>
    </div>

    <div class="os-title">
        <div><strong>ORDEM DE SERVIÇO:</strong> #{{ $order->os_number }}</div>
        <div><strong>Data de Entrada:</strong> {{ $order->entry_date->format('d/m/Y') }}</div>
        <div><strong>Status:</strong> {{ $order->status_label }}</div>
    </div>

    <div class="grid-2 section">
        <div>
            <div class="section-title">Dados do Cliente</div>
            <p><strong>Nome:</strong> {{ $order->customer_name }}</p>
            <p><strong>Telefone:</strong> {{ $order->customer_phone }}</p>
            @if($order->customer_document)<p><strong>CPF:</strong> {{ $order->customer_document }}</p>@endif
        </div>
        <div>
            <div class="section-title">Dados do Equipamento</div>
            <p><strong>Aparelho:</strong> {{ ucfirst($order->device_type) }} {{ $order->device_brand }} {{ $order->device_model }}</p>
            @if($order->device_serial)<p><strong>Serial/IMEI:</strong> {{ $order->device_serial }}</p>@endif
            @if($order->device_accessories)<p><strong>Acessórios:</strong> {{ $order->device_accessories }}</p>@endif
        </div>
    </div>

    @if($order->device_condition)
    <div class="section">
        <div class="section-title">Condições Físicas do Aparelho</div>
        <p>{{ $order->device_condition }}</p>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Defeito Relatado</div>
        <p>{{ $order->reported_defect }}</p>
    </div>

    @if($order->technical_diagnosis)
    <div class="section">
        <div class="section-title">Laudo Técnico / Serviços</div>
        <p>{{ $order->technical_diagnosis }}</p>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Orçamento & Valores</div>
        <table>
            <tr><th>Descrição</th><th style="text-align: right;">Valor</th></tr>
            <tr><td>Peças / Componentes</td><td style="text-align: right;">R$ {{ number_format($order->parts_cost, 2, ',', '.') }}</td></tr>
            <tr><td>Mão de Obra Especializada</td><td style="text-align: right;">R$ {{ number_format($order->labor_cost, 2, ',', '.') }}</td></tr>
            @if($order->discount_amount > 0)
            <tr><td>Desconto Concedido</td><td style="text-align: right; color: red;">- R$ {{ number_format($order->discount_amount, 2, ',', '.') }}</td></tr>
            @endif
            <tr style="font-size: 14px; font-weight: bold; background: #f0f0f0;">
                <td>VALOR TOTAL FINAL</td>
                <td style="text-align: right;">{{ $order->formatted_final_amount }}</td>
            </tr>
        </table>
    </div>

    <div class="terms">
        <strong>Termos de Garantia e Condições:</strong><br>
        1. A garantia dos serviços prestados é de 90 dias a contar da data de entrega, cobrindo exclusivamente as peças substituídas e o serviço executado.<br>
        2. Equipamentos não retirados em até 90 dias após aviso de conclusão estarão sujeitos a cobrança de taxa de guarda.<br>
        3. A empresa não se responsabiliza por dados, fotos e arquivos contidos no aparelho. Recomenda-se backup prévio pelo cliente.
    </div>

    <div class="signatures">
        <div class="signature-line">
            {{ $tenant->name ?? 'Assistência Técnica' }}<br>
            Técnico Responsável
        </div>
        <div class="signature-line">
            {{ $order->customer_name }}<br>
            Assinatura do Cliente
        </div>
    </div>
</body>
</html>
