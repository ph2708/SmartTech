@extends('layouts.admin')
@section('title', 'Controle Financeiro & Fluxo de Caixa')

@section('content')
<div class="dashboard">

    <!-- Header do Financeiro -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h3 style="margin: 0; font-size: 1.3rem;">💵 Fluxo de Caixa & Despesas</h3>
            <p class="help-text" style="margin-top: 4px;">Livro caixa geral. <em>(Vendas de balcão e Ordens de Serviço concluídas são somadas aqui automaticamente)</em>.</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('admin.financeiro.create', ['type' => 'income']) }}" class="btn btn-primary" style="background: #10b981;">+ Entrada Avulsa (Receita)</a>
            <a href="{{ route('admin.financeiro.create', ['type' => 'expense']) }}" class="btn btn-primary" style="background: #ef4444;">- Nova Despesa (Saída)</a>
        </div>
    </div>

    <!-- Cards de Resumo Financeiro (Mês Atual) -->
    <h4 style="margin-bottom: 12px; color: var(--text-light); font-size: 0.95rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">📅 Resumo do Mês Atual ({{ now()->translatedFormat('F/Y') }})</h4>
    <div class="stats-grid" style="margin-bottom: 24px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669)">📈</div>
            <div class="stat-info">
                <span class="stat-value" style="color: #059669;">R$ {{ number_format($monthIncome, 2, ',', '.') }}</span>
                <span class="stat-label">Entradas no Mês</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #b91c1c)">📉</div>
            <div class="stat-info">
                <span class="stat-value" style="color: #dc2626;">R$ {{ number_format($monthExpense, 2, ',', '.') }}</span>
                <span class="stat-label">Despesas no Mês</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)">⚖️</div>
            <div class="stat-info">
                <span class="stat-value" style="color: {{ $monthBalance >= 0 ? '#10b981' : '#ef4444' }};">
                    R$ {{ number_format($monthBalance, 2, ',', '.') }}
                </span>
                <span class="stat-label">Lucro Líquido no Mês</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706)">⚠️</div>
            <div class="stat-info">
                <span class="stat-value" style="color: #d97706;">R$ {{ number_format($pendingExpenses, 2, ',', '.') }}</span>
                <span class="stat-label">Despesas a Pagar (Pendentes)</span>
            </div>
        </div>
    </div>

    <!-- Tabela e Filtros de Lançamentos -->
    <div class="card">
        <div class="card-header">
            <h3>📑 Histórico de Lançamentos</h3>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <form method="GET" class="filters-bar">
                <select name="type" class="filter-select">
                    <option value="">Todos os Tipos</option>
                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>📈 Apenas Entradas (Receitas)</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>📉 Apenas Despesas (Saídas)</option>
                </select>
                <select name="status" class="filter-select">
                    <option value="">Todos os Status</option>
                    <option value="pago" {{ request('status') === 'pago' ? 'selected' : '' }}>Pago / Concluído</option>
                    <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente / A Pagar</option>
                </select>
                <input type="month" name="month" value="{{ request('month') }}" class="filter-input" placeholder="Mês">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="filter-input" placeholder="Data inicial">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="filter-input" placeholder="Data final">
                <button type="submit" class="btn btn-outline">🔍 Filtrar</button>
            </form>

            @if($transactions->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Valor</th>
                            <th>Pagamento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $t)
                        <tr>
                            <td>{{ $t->date->format('d/m/Y') }}</td>
                            <td>
                                @if($t->isIncome())
                                    <span class="status-badge" style="background: #dcfce7; color: #15803d; font-weight: bold;">📈 Entrada</span>
                                @else
                                    <span class="status-badge" style="background: #fee2e2; color: #dc2626; font-weight: bold;">📉 Despesa</span>
                                @endif
                            </td>
                            <td><strong>{{ $t->description }}</strong></td>
                            <td><span style="font-size: 0.85rem; color: #475569; background: #f1f5f9; padding: 3px 8px; border-radius: 4px;">{{ $t->category }}</span></td>
                            <td>
                                <strong style="font-size: 1rem; color: {{ $t->isIncome() ? '#16a34a' : '#dc2626' }};">
                                    {{ $t->isIncome() ? '+' : '-' }} {{ $t->formatted_amount }}
                                </strong>
                            </td>
                            <td>{{ $t->payment_method_label }}</td>
                            <td>
                                <span class="status-badge {{ $t->status === 'pago' ? 'active' : ($t->status === 'pendente' ? 'featured' : 'inactive') }}">
                                    {{ ucfirst($t->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.financeiro.edit', $t) }}" class="btn btn-sm btn-outline" title="Editar">✏️</a>
                                    <form method="POST" action="{{ route('admin.financeiro.destroy', $t) }}" onsubmit="return confirm('Excluir este lançamento financeiro?')">
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
                {{ $transactions->withQueryString()->links() }}
            </div>
            @else
            <div class="empty-state-admin">
                <span class="empty-icon">💵</span>
                <h3>Nenhum lançamento financeiro encontrado</h3>
                <p>Cadastre suas receitas e despesas para manter seu fluxo de caixa organizado.</p>
                <div style="display: flex; gap: 8px; justify-content: center;">
                    <a href="{{ route('admin.financeiro.create', ['type' => 'income']) }}" class="btn btn-primary" style="background: #10b981;">+ Nova Entrada</a>
                    <a href="{{ route('admin.financeiro.create', ['type' => 'expense']) }}" class="btn btn-primary" style="background: #ef4444;">- Nova Despesa</a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
