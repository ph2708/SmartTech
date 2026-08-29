@extends('layouts.admin')
@section('title', 'Nova Loja')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>🏢 Nova Loja</h3>
        <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('superadmin.tenants.store') }}" class="admin-form">
            @csrf

            <div class="form-section-title">Dados da Loja</div>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="name">Nome da Loja *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group flex-1">
                    <label for="slug">Slug (URL) *</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}" required placeholder="minha-loja">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="whatsapp">WhatsApp *</label>
                    <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" required>
                </div>
                <div class="form-group">
                    <label for="plan">Plano *</label>
                    <select id="plan" name="plan" required>
                        <option value="free" {{ old('plan') === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="basic" {{ old('plan') === 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="pro" {{ old('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                    </select>
                </div>
            </div>

            <div class="form-section-title">Administrador da Loja</div>

            <div class="form-group">
                <label for="admin_name">Nome do Administrador *</label>
                <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="admin_email">E-mail *</label>
                    <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                </div>
                <div class="form-group">
                    <label for="admin_password">Senha *</label>
                    <input type="password" id="admin_password" name="admin_password" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Criar Loja</button>
                <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
