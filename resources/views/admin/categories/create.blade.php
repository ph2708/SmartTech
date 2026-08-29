@extends('layouts.admin')
@section('title', 'Nova Categoria')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>📁 Nova Categoria</h3>
        <a href="{{ route('admin.categorias.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.categorias.store') }}" class="admin-form">
            @csrf
            <div class="form-group">
                <label for="name">Nome da Categoria *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Ex: Capinhas e Películas">
            </div>

            <div class="form-group">
                <label for="icon">Ícone (emoji)</label>
                <input type="text" id="icon" name="icon" value="{{ old('icon') }}" placeholder="Ex: 📱 🎧 💻 🛡️ 🧴" maxlength="10">
                <span class="help-text">Cole um emoji para representar a categoria</span>
            </div>

            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea id="description" name="description" rows="3" placeholder="Breve descrição da categoria">{{ old('description') }}</textarea>
            </div>

            <div class="form-check">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span>Categoria ativa</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Categoria</button>
                <a href="{{ route('admin.categorias.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
