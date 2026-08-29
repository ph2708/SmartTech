@extends('layouts.admin')
@section('title', 'Editar Usuário')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>✏️ Editar Usuário: {{ $user->name }}</h3>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.usuarios.update', $user) }}" class="admin-form">
            @csrf @method('PUT')

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="name">Nome Completo *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="form-group flex-1">
                    <label for="phone">Telefone / WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="email">E-mail de Acesso *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="form-group flex-1">
                    <label for="role">Função / Perfil *</label>
                    <select id="role" name="role" required>
                        <option value="tecnico" {{ old('role', $user->role) === 'tecnico' ? 'selected' : '' }}>🛠️ Técnico de Assistência</option>
                        <option value="atendente" {{ old('role', $user->role) === 'atendente' ? 'selected' : '' }}>💬 Atendente / Vendedor</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>👑 Administrador</option>
                    </select>
                </div>
                @if(isset($availableUnits) && $availableUnits->count() > 1)
                <div class="form-group flex-2">
                    <label for="tenant_id">Filial / Unidade de Trabalho *</label>
                    <select id="tenant_id" name="tenant_id" required>
                        @foreach($availableUnits as $unit)
                            <option value="{{ $unit->id }}" {{ old('tenant_id', $user->tenant_id) == $unit->id ? 'selected' : '' }}>
                                {{ $unit->is_branch ? '🏢 ' . ($unit->branch_name ?? $unit->name) : '👑 ' . $unit->name . ' (Matriz)' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="tenant_id" value="{{ $user->tenant_id }}">
                @endif
            </div>

            <div class="form-section-title">Alterar Senha (Opcional)</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Nova Senha</label>
                    <input type="password" id="password" name="password" placeholder="Deixe em branco para não alterar">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Nova Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repita a nova senha">
                </div>
            </div>

            @if($user->id !== auth()->id())
            <div class="form-check">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    <span>Usuário ativo (pode fazer login no sistema)</span>
                </label>
            </div>
            @endif

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
