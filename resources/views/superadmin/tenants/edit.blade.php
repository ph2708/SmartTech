@extends('layouts.admin')
@section('title', 'Editar Loja')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>✏️ Editar Loja: {{ $tenant->name }}</h3>
        <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('superadmin.tenants.update', $tenant) }}" class="admin-form">
            @csrf @method('PUT')

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="name">Nome da Loja *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $tenant->name) }}" required>
                </div>
                <div class="form-group flex-1">
                    <label>Slug</label>
                    <input type="text" value="{{ $tenant->slug }}" disabled class="input-disabled">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="whatsapp">WhatsApp *</label>
                    <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $tenant->whatsapp) }}" required>
                </div>
                <div class="form-group">
                    <label for="plan">Plano *</label>
                    <select id="plan" name="plan" required>
                        <option value="free" {{ $tenant->plan === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="basic" {{ $tenant->plan === 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="pro" {{ $tenant->plan === 'pro' ? 'selected' : '' }}>Pro</option>
                    </select>
                </div>
            </div>

            <div class="form-check">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}>
                    <span>Loja ativa</span>
                </label>
            </div>

            @if($tenant->users->count() > 0)
            <div class="form-section-title">Usuários</div>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Nome</th><th>E-mail</th><th>Role</th></tr></thead>
                    <tbody>
                        @foreach($tenant->users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
