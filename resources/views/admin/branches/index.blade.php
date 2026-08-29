@extends('layouts.admin')
@section('title', 'Filiais & Unidades da Loja')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3>🏢 Gestão de Filiais & Unidades</h3>
            <p class="help-text">Gerencie todas as filiais da rede <strong>{{ $mainTenant->name }}</strong> e alterne entre elas com 1 clique.</p>
        </div>
        <a href="{{ route('admin.filiais.create') }}" class="btn btn-primary">+ Nova Filial</a>
    </div>
    <div class="card-body">
        <div style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div>
                <span style="font-size: 0.8rem; background: #0284c7; color: white; padding: 3px 8px; border-radius: 4px; font-weight: bold;">UNIDADE ATIVA AGORA</span>
                <h4 style="margin: 6px 0 0 0; color: #0f172a;">{{ $tenant->name }}</h4>
                <p style="margin: 2px 0 0 0; font-size: 0.85rem; color: #64748b;">WhatsApp: {{ $tenant->whatsapp }} | {{ $tenant->address ?? 'Endereço não informado' }}</p>
            </div>
            @if($tenant->id !== $mainTenant->id)
            <a href="{{ route('admin.filiais.switch', $mainTenant) }}" class="btn btn-outline" style="background: white;">
                🔄 Alternar para Matriz
            </a>
            @endif
        </div>

        @if($branches->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome da Unidade</th>
                        <th>WhatsApp</th>
                        <th>Cidade / UF</th>
                        <th>Produtos Cadastrados</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background: #f8fafc;">
                        <td>
                            <strong>👑 {{ $mainTenant->name }} (Matriz)</strong>
                            @if($tenant->id === $mainTenant->id)
                                <span class="status-badge active" style="font-size: 0.75rem; margin-left: 6px;">Gerenciando Agora</span>
                            @endif
                        </td>
                        <td>{{ $mainTenant->whatsapp }}</td>
                        <td>{{ $mainTenant->city ? "{$mainTenant->city}/{$mainTenant->state}" : 'Matriz Principal' }}</td>
                        <td>{{ $mainTenant->products()->count() }} produtos</td>
                        <td>
                            @if($tenant->id !== $mainTenant->id)
                                <a href="{{ route('admin.filiais.switch', $mainTenant) }}" class="btn btn-sm btn-primary">Acessar Painel ➔</a>
                            @else
                                <span style="font-size: 0.85rem; color: #15803d; font-weight: bold;">✓ Ativa</span>
                            @endif
                        </td>
                    </tr>
                    @foreach($branches as $branch)
                    <tr>
                        <td>
                            <strong>🏢 {{ $branch->branch_name ?? $branch->name }}</strong>
                            @if($tenant->id === $branch->id)
                                <span class="status-badge active" style="font-size: 0.75rem; margin-left: 6px;">Gerenciando Agora</span>
                            @endif
                        </td>
                        <td>{{ $branch->whatsapp }}</td>
                        <td>{{ $branch->city ? "{$branch->city}/{$branch->state}" : '-' }}</td>
                        <td>{{ $branch->products_count }} produtos</td>
                        <td>
                            <div class="action-buttons">
                                @if($tenant->id !== $branch->id)
                                    <a href="{{ route('admin.filiais.switch', $branch) }}" class="btn btn-sm btn-outline" style="font-weight: bold;">Acessar Painel ➔</a>
                                @else
                                    <span style="font-size: 0.85rem; color: #15803d; font-weight: bold;">✓ Ativa</span>
                                @endif
                                <a href="{{ route('catalog.store', $branch->slug) }}" target="_blank" class="btn btn-sm btn-outline" title="Ver Catálogo Público">🌐</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state-admin">
            <span class="empty-icon">🏢</span>
            <h3>Sua loja ainda não possui filiais</h3>
            <p>Você pode cadastrar filiais, lojas secundárias ou quiosques com estoques e relatórios separados.</p>
            <a href="{{ route('admin.filiais.create') }}" class="btn btn-primary">+ Criar Primeira Filial</a>
        </div>
        @endif
    </div>
</div>
@endsection
