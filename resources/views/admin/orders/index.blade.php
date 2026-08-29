@extends('layouts.admin')
@section('title', 'Vendas e Pedidos')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3>🛒 Controle de Vendas & Balcão</h3>
            <p class="help-text">Vendas de produtos do catálogo com <strong>baixa automática de estoque</strong> e lançamento automático no financeiro.</p>
        </div>
        <a href="{{ route('admin.pedidos.create') }}" class="btn btn-primary">+ Nova Venda de Balcão</a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="filters-bar">
            <select name="status" class="filter-select">
                <option value="">Todos os status</option>
                <option value="concluido" {{ request('status') === 'concluido' ? 'selected' : '' }}>Concluídos</option>
                <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendentes</option>
                <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelados</option>
            </select>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="filter-input" placeholder="Data inicial">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="filter-input" placeholder="Data final">
            <button type="submit" class="btn btn-outline">🔍 Filtrar</button>
        </form>

        @if($orders->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Telefone / WhatsApp</th>
                        <th>Produto / Serviço</th>
                        <th>Valor</th>
                        <th>Pagamento</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->date->format('d/m/Y') }}</td>
                        <td><strong>{{ $order->customer_name ?? 'Consumidor Final' }}</strong></td>
                        <td>{{ $order->customer_phone ?? '-' }}</td>
                        <td>{{ $order->product->name ?? 'Venda Balcão / Outro' }}</td>
                        <td><strong style="color: var(--whatsapp);">{{ $order->formatted_amount }}</strong></td>
                        <td><span class="status-badge" style="background: #e2e8f0; color: #334155;">{{ $order->payment_method_label }}</span></td>
                        <td>
                            <span class="status-badge {{ $order->status === 'concluido' ? 'active' : ($order->status === 'pendente' ? 'featured' : 'inactive') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                @if($order->status === 'concluido')
                                    <form method="POST" action="{{ route('admin.invoices.emit', $order) }}" style="display: inline;" onsubmit="return confirm('Emitir Cupom Fiscal (NFC-e) via Focus NFe para esta venda?')">
                                        @csrf
                                        <input type="hidden" name="type" value="nfce">
                                        <button type="submit" class="btn btn-sm btn-outline" style="color: #0284c7; border-color: #bae6fd; font-weight: bold;" title="Emitir NFC-e (Focus NFe)">
                                            🧾 NFC-e
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.pedidos.edit', $order) }}" class="btn btn-sm btn-outline" title="Editar">✏️</a>
                                <form method="POST" action="{{ route('admin.pedidos.destroy', $order) }}" onsubmit="return confirm('Excluir esta venda?')">
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
            {{ $orders->withQueryString()->links() }}
        </div>
        @else
        <div class="empty-state-admin">
            <span class="empty-icon">🛒</span>
            <h3>Nenhuma venda registrada</h3>
            <p>Cadastre os pedidos e vendas dos seus clientes aqui.</p>
            <a href="{{ route('admin.pedidos.create') }}" class="btn btn-primary">+ Nova Venda</a>
        </div>
        @endif
    </div>
</div>
@endsection
