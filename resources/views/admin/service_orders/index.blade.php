@extends('layouts.admin')
@section('title', 'Ordens de Serviço (Assistência Técnica)')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3>🛠️ Gestão de Ordens de Serviço (OS)</h3>
            <p class="help-text">Total: <strong>{{ $totalOs }} OSs</strong> | Faturamento Concluído: <strong>R$ {{ number_format($faturamentoOs, 2, ',', '.') }}</strong></p>
        </div>
        <a href="{{ route('admin.ordens-servico.create') }}" class="btn btn-primary">+ Nova Ordem de Serviço</a>
    </div>
    <div class="card-body">

        <!-- Mini Dashboard de Status de OS -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 24px;">
            <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border-left: 4px solid #f59e0b; text-align: center;">
                <span style="font-size: 1.3rem; font-weight: 800; color: #b45309;">{{ $orcamentoCount }}</span>
                <p style="font-size: 0.75rem; color: #64748b; margin: 0; font-weight: 600;">Em Orçamento</p>
            </div>
            <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border-left: 4px solid #3b82f6; text-align: center;">
                <span style="font-size: 1.3rem; font-weight: 800; color: #1d4ed8;">{{ $emReparoCount }}</span>
                <p style="font-size: 0.75rem; color: #64748b; margin: 0; font-weight: 600;">Em Reparo</p>
            </div>
            <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border-left: 4px solid #8b5cf6; text-align: center;">
                <span style="font-size: 1.3rem; font-weight: 800; color: #6d28d9;">{{ $aguardandoPecaCount }}</span>
                <p style="font-size: 0.75rem; color: #64748b; margin: 0; font-weight: 600;">Aguardando Peça</p>
            </div>
            <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border-left: 4px solid #10b981; text-align: center;">
                <span style="font-size: 1.3rem; font-weight: 800; color: #047857;">{{ $prontoCount }}</span>
                <p style="font-size: 0.75rem; color: #64748b; margin: 0; font-weight: 600;">Pronto p/ Retirada</p>
            </div>
            <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border-left: 4px solid #64748b; text-align: center;">
                <span style="font-size: 1.3rem; font-weight: 800; color: #334155;">{{ $entregueCount }}</span>
                <p style="font-size: 0.75rem; color: #64748b; margin: 0; font-weight: 600;">Entregues</p>
            </div>
        </div>

        <!-- Filtros de Busca -->
        <form method="GET" class="filters-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por Nº OS, Cliente, Celular, Marca..." class="filter-input">
            <select name="status" class="filter-select">
                <option value="">Todos os Status</option>
                <option value="orcamento" {{ request('status') === 'orcamento' ? 'selected' : '' }}>Em Orçamento</option>
                <option value="aguardando_peca" {{ request('status') === 'aguardando_peca' ? 'selected' : '' }}>Aguardando Peça</option>
                <option value="aprovado" {{ request('status') === 'aprovado' ? 'selected' : '' }}>Aprovado / Em Reparo</option>
                <option value="pronto" {{ request('status') === 'pronto' ? 'selected' : '' }}>Pronto p/ Retirada</option>
                <option value="entregue" {{ request('status') === 'entregue' ? 'selected' : '' }}>Entregue / Concluído</option>
                <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
            </select>
            <select name="device_type" class="filter-select">
                <option value="">Todos os Aparelhos</option>
                <option value="celular" {{ request('device_type') === 'celular' ? 'selected' : '' }}>📱 Celular</option>
                <option value="computador" {{ request('device_type') === 'computador' ? 'selected' : '' }}>🖥️ Computador</option>
                <option value="notebook" {{ request('device_type') === 'notebook' ? 'selected' : '' }}>💻 Notebook</option>
                <option value="tablet" {{ request('device_type') === 'tablet' ? 'selected' : '' }}>📟 Tablet</option>
            </select>
            <button type="submit" class="btn btn-outline">🔍 Filtrar</button>
        </form>

        @if($serviceOrders->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nº OS</th>
                        <th>Entrada</th>
                        <th>Cliente</th>
                        <th>Equipamento</th>
                        <th>Defeito Relatado</th>
                        <th>Valor Final</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($serviceOrders as $os)
                    <tr>
                        <td><strong style="color: var(--primary);">#{{ $os->os_number }}</strong></td>
                        <td>{{ $os->entry_date->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $os->customer_name }}</strong><br>
                            <small style="color: var(--text-light);">{{ $os->customer_phone }}</small>
                        </td>
                        <td>{{ $os->device_type_icon }} <strong>{{ $os->device_brand }}</strong> {{ $os->device_model }}</td>
                        <td><small>{{ Str::limit($os->reported_defect, 45) }}</small></td>
                        <td><strong style="color: var(--text);">{{ $os->formatted_final_amount }}</strong></td>
                        <td>
                            <span class="status-badge {{ $os->status_badge_class }}">
                                {{ $os->status_label }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                @if(in_array($os->status, ['pronto', 'entregue']))
                                    <form method="POST" action="{{ route('admin.invoices.emitServiceOrder', $os) }}" style="display: inline;" onsubmit="return confirm('Emitir Nota Fiscal de Serviços (NFS-e) via Focus NFe para esta OS?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline" style="color: #7c3aed; border-color: #ddd6fe; font-weight: bold;" title="Emitir NFS-e de Serviço (Focus NFe)">
                                            🧾 NFS-e
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.ordens-servico.show', $os) }}" class="btn btn-sm btn-outline" title="Ver Detalhes">👁️</a>
                                <a href="{{ route('admin.ordens-servico.edit', $os) }}" class="btn btn-sm btn-outline" title="Editar">✏️</a>
                                <a href="{{ route('admin.ordens-servico.print', $os) }}" target="_blank" class="btn btn-sm btn-outline" title="Imprimir Comprovante">🖨️</a>
                                <a href="{{ $os->whatsapp_notify_url }}" target="_blank" class="btn btn-sm btn-outline" title="Avisar Cliente no WhatsApp" style="color: var(--whatsapp);">💬</a>
                                <form method="POST" action="{{ route('admin.ordens-servico.destroy', $os) }}" onsubmit="return confirm('Tem certeza que deseja excluir esta OS?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Excluir">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $serviceOrders->withQueryString()->links() }}
        </div>
        @else
        <div class="empty-state-admin">
            <span class="empty-icon">🛠️</span>
            <h3>Nenhuma Ordem de Serviço encontrada</h3>
            <p>Cadastre as ordens de serviço dos aparelhos recebidos na loja.</p>
            <a href="{{ route('admin.ordens-servico.create') }}" class="btn btn-primary">+ Nova Ordem de Serviço</a>
        </div>
        @endif
    </div>
</div>
@endsection
