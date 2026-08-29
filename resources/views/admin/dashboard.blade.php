@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard">

    @if($lowStockProductsCount > 0)
    <div class="alert alert-error" style="margin: 0 0 20px 0; background: #fff3cd; color: #856404; border: 1px solid #ffeeba;">
        <span>⚠️</span> <strong>Atenção:</strong> Você possui <strong>{{ $lowStockProductsCount }}</strong> {{ $lowStockProductsCount === 1 ? 'produto' : 'produtos' }} com estoque baixo ou esgotado!
        <a href="{{ route('admin.produtos.index', ['stock_filter' => 'low']) }}" style="text-decoration: underline; font-weight: bold; margin-left: 8px;">Ver produtos →</a>
    </div>
    @endif

    <!-- Assistência Técnica (Ordens de Serviço) -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <h4 style="color: var(--text-light); font-size: 0.95rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">🛠️ Assistência Técnica (Ordens de Serviço)</h4>
        <a href="{{ route('admin.ordens-servico.create') }}" class="btn btn-sm btn-primary">+ Nova OS</a>
    </div>
    <div class="stats-grid" style="margin-bottom: 24px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c)">📋</div>
            <div class="stat-info">
                <span class="stat-value">{{ $totalOsCount }}</span>
                <span class="stat-label">Total de OS Abertas</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f6d365, #fda085)">⏳</div>
            <div class="stat-info">
                <span class="stat-value">{{ $activeOsCount }}</span>
                <span class="stat-label">Em Reparo / Orçamento</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #11998e, #38ef7d)">🎉</div>
            <div class="stat-info">
                <span class="stat-value">{{ $readyOsCount }}</span>
                <span class="stat-label">Prontos p/ Retirada</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe)">📦</div>
            <div class="stat-info">
                <span class="stat-value">{{ $lowStockProductsCount }}</span>
                <span class="stat-label">Alertas de Estoque Baixo</span>
            </div>
        </div>
    </div>

    <!-- Métricas Financeiras & Fluxo de Caixa (Mês Atual) -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <h4 style="color: var(--text-light); font-size: 0.95rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">💰 Financeiro do Mês ({{ now()->translatedFormat('F/Y') }})</h4>
        <a href="{{ route('admin.financeiro.index') }}" class="btn btn-sm btn-outline">Fluxo Completo →</a>
    </div>
    <div class="stats-grid" style="margin-bottom: 24px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669)">📈</div>
            <div class="stat-info">
                <span class="stat-value" style="color: #059669;">R$ {{ number_format($monthIncome, 2, ',', '.') }}</span>
                <span class="stat-label">Entradas (Receitas)</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #b91c1c)">📉</div>
            <div class="stat-info">
                <span class="stat-value" style="color: #dc2626;">R$ {{ number_format($monthExpense, 2, ',', '.') }}</span>
                <span class="stat-label">Despesas (Saídas)</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)">⚖️</div>
            <div class="stat-info">
                <span class="stat-value" style="color: {{ $monthNetProfit >= 0 ? '#10b981' : '#dc2626' }};">
                    R$ {{ number_format($monthNetProfit, 2, ',', '.') }}
                </span>
                <span class="stat-label">Lucro Líquido do Mês</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #25D366, #128C7E)">💬</div>
            <div class="stat-info">
                <span class="stat-value">{{ $totalWhatsAppClicks }}</span>
                <span class="stat-label">Cliques WhatsApp (Hoje: {{ $todayWhatsAppClicks }})</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3>🚀 Ações Rápidas</h3>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                <a href="{{ route('admin.ordens-servico.create') }}" class="quick-action-btn" style="background: linear-gradient(135deg, rgba(230,57,70,0.1), rgba(255,107,122,0.1)); border: 1px solid rgba(230,57,70,0.3);">
                    <span class="qa-icon">🔧</span>
                    <span><strong>Nova Ordem de Serviço</strong></span>
                </a>
                <a href="{{ route('admin.produtos.create') }}" class="quick-action-btn">
                    <span class="qa-icon">📦</span>
                    <span>Novo Produto / Item</span>
                </a>
                <a href="{{ route('admin.pedidos.create') }}" class="quick-action-btn">
                    <span class="qa-icon">💵</span>
                    <span>Registrar Venda</span>
                </a>
                <a href="{{ route('admin.categorias.create') }}" class="quick-action-btn">
                    <span class="qa-icon">📁</span>
                    <span>Nova Categoria</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Comparativo de Vendas por Filial (Exibido apenas quando houver filiais cadastradas) -->
    @if(isset($hasBranches) && $hasBranches)
    <div class="card" style="border-left: 4px solid #3b82f6;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3>🏢 Comparativo de Vendas & Faturamento da Rede (Mês Atual)</h3>
                <p class="help-text">Acompanhe em tempo real o desempenho de vendas de produtos e ordens de serviço de cada unidade.</p>
            </div>
            <a href="{{ route('admin.filiais.index') }}" class="btn btn-sm btn-outline">Gerenciar Filiais →</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Unidade / Filial</th>
                            <th>Vendas Balcão (R$)</th>
                            <th>Serviços OS (R$)</th>
                            <th>Faturamento Total</th>
                            <th>Qtd. Vendas</th>
                            <th>Qtd. OS Entregues</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branchesSales as $bs)
                        <tr style="{{ $bs['is_current'] ? 'background: #f0fdf4;' : '' }}">
                            <td>
                                <strong>{{ $bs['name'] }}</strong>
                                @if($bs['is_current'])
                                    <span class="status-badge active" style="font-size: 0.7rem; margin-left: 6px;">Unidade Atual</span>
                                @endif
                            </td>
                            <td>R$ {{ number_format($bs['sales_amount'], 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($bs['os_amount'], 2, ',', '.') }}</td>
                            <td>
                                <strong style="color: #059669; font-size: 1rem;">
                                    R$ {{ number_format($bs['total_revenue'], 2, ',', '.') }}
                                </strong>
                            </td>
                            <td>{{ $bs['orders_count'] }} vendas</td>
                            <td>{{ $bs['os_count'] }} OS</td>
                            <td>
                                @if(!$bs['is_current'])
                                    <a href="{{ route('admin.filiais.switch', $bs['id']) }}" class="btn btn-sm btn-outline" style="font-size: 0.75rem; font-weight: bold;">
                                        Acessar Painel ➔
                                    </a>
                                @else
                                    <span style="font-size: 0.8rem; color: #15803d; font-weight: bold;">✓ Gerenciando</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Painel de Ordens de Serviço Recentes -->
    <div class="card">
        <div class="card-header">
            <h3>🛠️ Ordens de Serviço Recentes (Assistência Técnica)</h3>
            <a href="{{ route('admin.ordens-servico.index') }}" class="btn btn-sm btn-outline">Ver Todas as OS →</a>
        </div>
        <div class="card-body">
            @if($recentOs->count() > 0)
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
                        @foreach($recentOs as $os)
                        <tr>
                            <td><strong style="color: var(--primary);">#{{ $os->os_number }}</strong></td>
                            <td>{{ $os->entry_date->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $os->customer_name }}</strong><br>
                                <small style="color: var(--text-light);">{{ $os->customer_phone }}</small>
                            </td>
                            <td>{{ $os->device_type_icon }} {{ $os->device_brand }} {{ $os->device_model }}</td>
                            <td><small>{{ Str::limit($os->reported_defect, 40) }}</small></td>
                            <td><strong>{{ $os->formatted_final_amount }}</strong></td>
                            <td><span class="status-badge {{ $os->status_badge_class }}">{{ $os->status_label }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.ordens-servico.show', $os) }}" class="btn btn-sm btn-outline" title="Ver Detalhes">👁️</a>
                                    <a href="{{ $os->whatsapp_notify_url }}" target="_blank" class="btn btn-sm btn-outline" title="Avisar no WhatsApp" style="color: var(--whatsapp);">💬</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state-admin" style="padding: 30px;">
                <p>Nenhuma ordem de serviço aberta. Crie sua primeira OS para controlar os reparos.</p>
                <a href="{{ route('admin.ordens-servico.create') }}" class="btn btn-sm btn-primary">+ Criar Primeira OS</a>
            </div>
            @endif
        </div>
    </div>

    <!-- Store Link -->
    @if($tenant)
    <div class="card">
        <div class="card-header">
            <h3>🔗 Link do Seu Catálogo</h3>
        </div>
        <div class="card-body">
            <div class="store-link-box">
                <input type="text" value="{{ config('app.single_store_mode') ? url('/') : url('/loja/' . $tenant->slug) }}" id="storeLink" readonly>
                <button onclick="navigator.clipboard.writeText(document.getElementById('storeLink').value); this.textContent='✅ Copiado!'; setTimeout(() => this.textContent='📋 Copiar', 2000)" class="btn btn-primary">📋 Copiar</button>
            </div>
            <p class="help-text">Compartilhe esse link com seus clientes para que acessem seu catálogo.</p>
        </div>
    </div>
    @endif
</div>
@endsection
