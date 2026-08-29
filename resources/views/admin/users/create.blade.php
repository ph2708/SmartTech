@extends('layouts.admin')
@section('title', 'Novo Usuário')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>👤 Novo Usuário / Membro da Equipe</h3>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.usuarios.store') }}" class="admin-form">
            @csrf

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="name">Nome Completo *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Ex: Lucas Técnico">
                </div>
                <div class="form-group flex-1">
                    <label for="phone">Telefone / WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="64 99999-9999">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="email">E-mail de Acesso (Login) *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="funcionario@smarttech.com">
                </div>
                <div class="form-group flex-1">
                    <label for="role">Função / Perfil *</label>
                    <select id="role" name="role" required>
                        <option value="tecnico" {{ old('role') === 'tecnico' ? 'selected' : '' }}>🛠️ Técnico de Assistência</option>
                        <option value="atendente" {{ old('role') === 'atendente' ? 'selected' : '' }}>💬 Atendente / Vendedor</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>👑 Administrador</option>
                    </select>
                </div>
                @if(isset($availableUnits) && $availableUnits->count() > 1)
                <div class="form-group flex-2">
                    <label for="tenant_id">Filial / Unidade de Trabalho *</label>
                    <select id="tenant_id" name="tenant_id" required>
                        @foreach($availableUnits as $unit)
                            <option value="{{ $unit->id }}" {{ old('tenant_id', $currentTenant->id) == $unit->id ? 'selected' : '' }}>
                                {{ $unit->is_branch ? '🏢 ' . ($unit->branch_name ?? $unit->name) : '👑 ' . $unit->name . ' (Matriz)' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="tenant_id" value="{{ $currentTenant->id }}">
                @endif
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Senha de Acesso *</label>
                    <input type="password" id="password" name="password" required placeholder="Mínimo 6 caracteres">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Senha *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repita a senha">
                </div>
            </div>

            <div class="form-check">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span>Usuário ativo (pode fazer login no sistema)</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cadastrar Usuário</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
