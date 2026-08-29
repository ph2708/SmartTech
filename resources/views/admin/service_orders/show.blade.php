@extends('layouts.admin')
@section('title', 'Detalhes da OS #' . $order->os_number)

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3>🛠️ Ordem de Serviço #{{ $order->os_number }}</h3>
            <span class="status-badge {{ $order->status_badge_class }}" style="font-size: 0.9rem;">{{ $order->status_label }}</span>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.ordens-servico.print', $order) }}" target="_blank" class="btn btn-outline">🖨️ Imprimir Comprovante</a>
            <a href="{{ $order->whatsapp_notify_url }}" target="_blank" class="btn btn-primary" style="background: var(--whatsapp);">💬 Avisar Cliente no WhatsApp</a>
            <a href="{{ route('admin.ordens-servico.edit', $order) }}" class="btn btn-outline">✏️ Editar</a>
            <a href="{{ route('admin.ordens-servico.index') }}" class="btn btn-outline">← Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <!-- Dados do Cliente -->
            <div style="background: #f8fafc; padding: 20px; border-radius: 10px; border: 1px solid var(--border);">
                <h4 style="margin-bottom: 12px; font-weight: 700; color: var(--primary);">👤 Dados do Cliente</h4>
                <p><strong>Nome:</strong> {{ $order->customer_name }}</p>
                <p><strong>WhatsApp / Telefone:</strong> {{ $order->customer_phone }}</p>
                @if($order->customer_email)<p><strong>E-mail:</strong> {{ $order->customer_email }}</p>@endif
                @if($order->customer_document)<p><strong>CPF:</strong> {{ $order->customer_document }}</p>@endif
            </div>

            <!-- Dados do Equipamento -->
            <div style="background: #f8fafc; padding: 20px; border-radius: 10px; border: 1px solid var(--border);">
                <h4 style="margin-bottom: 12px; font-weight: 700; color: var(--primary);">📱 Equipamento / Aparelho</h4>
                <p><strong>Tipo:</strong> {{ $order->device_type_icon }} {{ ucfirst($order->device_type) }}</p>
                <p><strong>Marca / Modelo:</strong> <strong>{{ $order->device_brand }}</strong> {{ $order->device_model }}</p>
                @if($order->device_serial)<p><strong>IMEI / Serial:</strong> {{ $order->device_serial }}</p>@endif
                @if($order->device_password)<p><strong>Senha:</strong> <code>{{ $order->device_password }}</code></p>@endif
                @if($order->device_accessories)<p><strong>Acessórios:</strong> {{ $order->device_accessories }}</p>@endif
            </div>
        </div>

        @if($order->device_condition)
        <div style="margin-bottom: 20px; padding: 16px; background: #fff8e6; border-radius: 8px; border-left: 4px solid #f59e0b;">
            <strong>Condições Físicas na Entrada:</strong>
            <p style="margin: 4px 0 0 0;">{{ $order->device_condition }}</p>
        </div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <!-- Defeito e Diagnóstico -->
            <div>
                <h4 style="font-weight: 700; margin-bottom: 8px;">Defeito Relatado</h4>
                <p style="background: white; padding: 12px; border-radius: 8px; border: 1px solid var(--border);">{{ $order->reported_defect }}</p>

                @if($order->technical_diagnosis)
                <h4 style="font-weight: 700; margin: 16px 0 8px;">Laudo / Diagnóstico Técnico</h4>
                <p style="background: white; padding: 12px; border-radius: 8px; border: 1px solid var(--border);">{{ $order->technical_diagnosis }}</p>
                @endif

                @if($order->services_description)
                <h4 style="font-weight: 700; margin: 16px 0 8px;">Serviços Realizados</h4>
                <p style="background: white; padding: 12px; border-radius: 8px; border: 1px solid var(--border);">{{ $order->services_description }}</p>
                @endif
            </div>

            <!-- Valores & Prazos -->
            <div style="background: #f8fafc; padding: 20px; border-radius: 10px; border: 1px solid var(--border);">
                <h4 style="margin-bottom: 16px; font-weight: 700; color: var(--text);">💰 Resumo Financeiro & Prazos</h4>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
                    <tr><td style="padding: 6px 0;">Data de Entrada:</td><td style="text-align: right;"><strong>{{ $order->entry_date->format('d/m/Y') }}</strong></td></tr>
                    @if($order->estimated_date)<tr><td style="padding: 6px 0;">Previsão de Entrega:</td><td style="text-align: right;"><strong>{{ $order->estimated_date->format('d/m/Y') }}</strong></td></tr>@endif
                    @if($order->completion_date)<tr><td style="padding: 6px 0;">Data de Conclusão:</td><td style="text-align: right;"><strong>{{ $order->completion_date->format('d/m/Y') }}</strong></td></tr>@endif
                    <tr style="border-top: 1px solid var(--border);"><td style="padding: 8px 0;">Custo Peças:</td><td style="text-align: right;">R$ {{ number_format($order->parts_cost, 2, ',', '.') }}</td></tr>
                    <tr><td style="padding: 6px 0;">Mão de Obra:</td><td style="text-align: right;">R$ {{ number_format($order->labor_cost, 2, ',', '.') }}</td></tr>
                    @if($order->discount_amount > 0)<tr><td style="padding: 6px 0; color: #dc2626;">Desconto:</td><td style="text-align: right; color: #dc2626;">- R$ {{ number_format($order->discount_amount, 2, ',', '.') }}</td></tr>@endif
                    <tr style="border-top: 2px solid var(--border); font-size: 1.2rem;"><td style="padding: 10px 0;"><strong>Valor Total:</strong></td><td style="text-align: right;"><strong style="color: var(--whatsapp);">{{ $order->formatted_final_amount }}</strong></td></tr>
                </table>

                <p><strong>Forma de Pagamento:</strong> {{ ucfirst($order->payment_method) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
