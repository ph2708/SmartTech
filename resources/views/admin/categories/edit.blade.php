@extends('layouts.admin')
@section('title', 'Editar Categoria')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>✏️ Editar Categoria</h3>
        <a href="{{ route('admin.categorias.index') }}" class="btn btn-outline">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.categorias.update', $category) }}" class="admin-form">
            @csrf @method('PUT')
            <div class="form-group">
                <label for="name">Nome da Categoria *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
            </div>

            <div class="form-group">
                <label for="icon">Ícone (emoji)</label>
                <input type="text" id="icon" name="icon" value="{{ old('icon', $category->icon) }}" placeholder="Ex: 📱 🎧 💻" maxlength="10">
            </div>

            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="form-check">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <span>Categoria ativa</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="{{ route('admin.categorias.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
