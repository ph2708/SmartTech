@extends('layouts.admin')
@section('title', 'Notas Fiscais Emitidas')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3>🧾 Notas Fiscais Eletrônicas (Focus NFe)</h3>
            <p class="help-text">Histórico de emissões de NFC-e (Cupom Fiscal) e NF-e com links diretos para DANFE (PDF) e XML da SEFAZ.</p>
        </div>
        <div>
            @if($tenant->nfe_enabled)
                <span class="status-badge active" style="font-weight: bold;">
                    ⚡ Focus NFe Ativa ({{ strtoupper($tenant->nfe_environment ?? 'homologacao') }})
                </span>
            @else
                <a href="{{ route('admin.configuracoes.edit') }}" class="btn btn-outline" style="color: #ea580c; border-color: #fdba74;">
                    ⚙️ Configurar Token Focus NFe
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if($invoices->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nº / Ref</th>
                        <th>Tipo</th>
                        <th>Cliente</th>
                        <th>Valor (R$)</th>
                        <th>Status SEFAZ</th>
                        <th>Data Emissão</th>
                        <th>Arquivos / Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $inv)
                    <tr>
                        <td>
                            <strong>{{ $inv->nfe_number ? "Nº {$inv->nfe_number}" : $inv->reference_code }}</strong>
                            @if($inv->nfe_series)<span style="font-size: 0.75rem; color: #64748b;">(Série {{ $inv->nfe_series }})</span>@endif
                        </td>
                        <td>
                            <span style="font-size: 0.8rem; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-weight: bold;">
                                {{ strtoupper($inv->type) }}
                            </span>
                        </td>
                        <td>{{ $inv->customer_name ?? 'Consumidor Final' }}</td>
                        <td><strong>R$ {{ number_format($inv->total_amount, 2, ',', '.') }}</strong></td>
                        <td>
                            @if($inv->status === 'autorizado')
                                <span class="status-badge active">✓ Autorizada</span>
                            @elseif($inv->status === 'processando')
                                <span class="status-badge" style="background: #fef3c7; color: #b45309;">⏳ Processando</span>
                            @else
                                <span class="status-badge inactive" title="{{ $inv->error_message }}">❌ Rejeitada</span>
                            @endif
                        </td>
                        <td>{{ $inv->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="action-buttons">
                                @if($inv->pdf_url)
                                    <a href="{{ $inv->pdf_url }}" target="_blank" class="btn btn-sm btn-primary" title="Imprimir DANFE (PDF)">
                                        📄 DANFE
                                    </a>
                                @endif
                                @if($inv->xml_url)
                                    <a href="{{ $inv->xml_url }}" target="_blank" class="btn btn-sm btn-outline" title="Baixar XML">
                                        📥 XML
                                    </a>
                                @endif
                                @if($inv->status === 'processando')
                                    <form method="POST" action="{{ route('admin.invoices.sync', $inv) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline" title="Sincronizar com SEFAZ">🔄</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">
            {{ $invoices->links() }}
        </div>
        @else
        <div class="empty-state-admin">
            <span class="empty-icon">🧾</span>
            <h3>Nenhuma nota fiscal emitida ainda</h3>
            <p>Você pode emitir NFC-e ou NF-e diretamente a partir de qualquer venda no menu Vendas / Pedidos.</p>
            <a href="{{ route('admin.pedidos.index') }}" class="btn btn-primary">Ir para Vendas</a>
        </div>
        @endif
    </div>
</div>
@endsection
