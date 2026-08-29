@extends('layouts.admin')
@section('title', 'Categorias')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>📁 Categorias</h3>
        <a href="{{ route('admin.categorias.create') }}" class="btn btn-primary">+ Nova Categoria</a>
    </div>
    <div class="card-body">
        @if($categories->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ordem</th>
                        <th>Ícone</th>
                        <th>Nome</th>
                        <th>Produtos</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td><span class="order-badge">{{ $category->order }}</span></td>
                        <td><span class="category-icon">{{ $category->icon ?? '📁' }}</span></td>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td><span class="count-badge">{{ $category->products_count }}</span></td>
                        <td>
                            <span class="status-badge {{ $category->is_active ? 'active' : 'inactive' }}">
                                {{ $category->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.categorias.edit', $category) }}" class="btn btn-sm btn-outline" title="Editar">✏️</a>
                                @if($category->products_count === 0)
                                <form method="POST" action="{{ route('admin.categorias.destroy', $category) }}" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
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
        @else
        <div class="empty-state-admin">
            <span class="empty-icon">📁</span>
            <h3>Nenhuma categoria cadastrada</h3>
            <p>Crie sua primeira categoria para começar a adicionar produtos.</p>
            <a href="{{ route('admin.categorias.create') }}" class="btn btn-primary">+ Nova Categoria</a>
        </div>
        @endif
    </div>
</div>
@endsection
