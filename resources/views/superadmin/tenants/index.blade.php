@extends('layouts.admin')
@section('title', 'Gerenciar Lojas')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>🏢 Gerenciar Lojas</h3>
        <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary">+ Nova Loja</a>
    </div>
    <div class="card-body">
        @if($tenants->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Loja</th>
                        <th>Slug</th>
                        <th>WhatsApp</th>
                        <th>Plano</th>
                        <th>Produtos</th>
                        <th>Usuários</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenants as $tenant)
                    <tr>
                        <td><strong>{{ $tenant->name }}</strong></td>
                        <td><code>{{ $tenant->slug }}</code></td>
                        <td>
                            <a href="https://wa.me/55{{ preg_replace('/\D/', '', $tenant->whatsapp) }}" target="_blank" style="color: #10b981; font-weight: bold; text-decoration: none;" title="Abrir conversa no WhatsApp">
                                💬 {{ $tenant->whatsapp }}
                            </a>
                        </td>
                        <td><span class="plan-badge {{ $tenant->plan }}">{{ ucfirst($tenant->plan) }}</span></td>
                        <td>{{ $tenant->products_count }}</td>
                        <td>{{ $tenant->users_count }}</td>
                        <td>
                            <span class="status-badge {{ $tenant->is_active ? 'active' : 'inactive' }}">
                                {{ $tenant->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('catalog.store', $tenant->slug) }}" target="_blank" class="btn btn-sm btn-outline" title="Ver loja pública">🌐</a>
                                <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="btn btn-sm btn-outline" title="Editar">✏️</a>
                                <form method="POST" action="{{ route('superadmin.tenants.destroy', $tenant) }}" onsubmit="return confirm('ATENÇÃO: Isso excluirá a loja e todos os dados. Continuar?')">
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
        @else
        <div class="empty-state-admin">
            <span class="empty-icon">🏢</span>
            <h3>Nenhuma loja cadastrada</h3>
            <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary">+ Nova Loja</a>
        </div>
        @endif
    </div>
</div>
@endsection
