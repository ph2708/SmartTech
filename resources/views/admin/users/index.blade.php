@extends('layouts.admin')
@section('title', 'Usuários & Equipe da Loja')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3>👥 Usuários & Equipe da Loja</h3>
            <p class="help-text">Gerencie os acessos de administradores, técnicos de assistência e atendentes à loja.</p>
        </div>
        <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">+ Novo Usuário</a>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Unidade / Filial</th>
                        <th>E-mail</th>
                        <th>Telefone / WhatsApp</th>
                        <th>Perfil / Função</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            @if($user->id === auth()->id())
                                <span style="font-size: 0.75rem; background: #e0f2fe; color: #0284c7; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">(Você)</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size: 0.85rem; color: #475569; background: #f1f5f9; padding: 3px 8px; border-radius: 4px;">
                                {{ $user->tenant?->is_branch ? '🏢 ' . ($user->tenant->branch_name ?? $user->tenant->name) : '👑 ' . ($user->tenant->name ?? 'Matriz') }}
                            </span>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            @if($user->isAdmin())
                                <span class="status-badge" style="background: #fef3c7; color: #b45309; font-weight: bold;">👑 {{ $user->role_label }}</span>
                            @elseif($user->isTechnician())
                                <span class="status-badge" style="background: #e0e7ff; color: #4338ca; font-weight: bold;">🛠️ {{ $user->role_label }}</span>
                            @else
                                <span class="status-badge" style="background: #dcfce7; color: #15803d; font-weight: bold;">💬 {{ $user->role_label }}</span>
                            @endif
                        </td>
                        <td>
                            @if($user->id === auth()->id())
                                <span class="status-badge active">Ativo</span>
                            @else
                                <button class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}"
                                        onclick="toggleUserStatus({{ $user->id }}, this)"
                                        title="Clique para alterar acesso">
                                    {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                </button>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.usuarios.edit', $user) }}" class="btn btn-sm btn-outline" title="Editar">✏️</a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.usuarios.destroy', $user) }}" onsubmit="return confirm('Remover o acesso deste usuário?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Excluir">🗑️</button>
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
            {{ $users->links() }}
        </div>
        @else
        <div class="empty-state-admin">
            <span class="empty-icon">👥</span>
            <h3>Nenhum membro na equipe</h3>
            <p>Cadastre técnicos e atendentes para operarem o sistema da sua loja.</p>
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">+ Novo Usuário</a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleUserStatus(userId, btn) {
    fetch(`/admin/usuarios/${userId}/toggle-active`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.className = 'status-badge ' + (data.is_active ? 'active' : 'inactive');
            btn.textContent = data.is_active ? 'Ativo' : 'Inativo';
        }
    });
}
</script>
@endsection
