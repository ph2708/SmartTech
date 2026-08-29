@extends('layouts.admin')
@section('title', 'Cadastrar Nova Filial')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3>🏢 Nova Filial para {{ $mainTenant->name }}</h3>
            <p class="help-text">A nova filial terá seu próprio catálogo, controle de estoque independente e WhatsApp próprio.</p>
        </div>
        <a href="{{ route('admin.filiais.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.filiais.store') }}" class="admin-form">
            @csrf

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="branch_name">Nome da Unidade / Filial *</label>
                    <input type="text" id="branch_name" name="branch_name" value="{{ old('branch_name') }}" required placeholder="Ex: Filial Centro, Loja Shopping, Quiosque 2">
                </div>
                <div class="form-group flex-1">
                    <label for="whatsapp">WhatsApp da Filial *</label>
                    <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $mainTenant->whatsapp) }}" required placeholder="64 99999-9999">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="address">Endereço da Filial</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="Av. Principal, nº 100">
                </div>
                <div class="form-group flex-1">
                    <label for="city">Cidade</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $mainTenant->city) }}">
                </div>
                <div class="form-group" style="max-width: 90px;">
                    <label for="state">UF</label>
                    <input type="text" id="state" name="state" value="{{ old('state', $mainTenant->state) }}" maxlength="2">
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 16px; border-radius: 8px; margin: 16px 0;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                    <input type="checkbox" name="copy_products" value="1" checked style="width: 18px; height: 18px;">
                    <div>
                        <strong style="color: #0f172a;">Copiar produtos e categorias da Matriz automaticamente?</strong>
                        <p style="margin: 2px 0 0 0; font-size: 0.85rem; color: #64748b;">Os produtos serão duplicados para a nova filial com estoque zerado para que você preencha a quantidade local.</p>
                    </div>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Criar Filial</button>
                <a href="{{ route('admin.filiais.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
