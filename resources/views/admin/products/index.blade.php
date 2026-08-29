@extends('layouts.admin')
@section('title', 'Produtos e Estoque')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3>📦 Produtos, Serviços, Estoque & Patrimônio</h3>
            <p class="help-text">
                Produtos Físicos: <strong>{{ $totalProductsCount }}</strong> | Serviços: <strong>{{ $totalServicesCount }}</strong> | Imobilizados: <strong>{{ $totalAssetsCount ?? 0 }}</strong>
                @if($lowStockCount > 0)
                    | <strong style="color: #dc2626;">⚠️ {{ $lowStockCount }} itens com estoque baixo/zerado</strong>
                @endif
            </p>
        </div>
        <a href="{{ route('admin.produtos.create') }}" class="btn btn-primary">+ Novo Produto / Serviço</a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="filters-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar produto..." class="filter-input">
            <select name="category" class="filter-select">
                <option value="">Todas as categorias</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="type" class="filter-select">
                <option value="">Todos os Tipos</option>
                <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>📦 Produto Físico (Mercadoria)</option>
                <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>🔧 Serviço (Assistência)</option>
                <option value="asset" {{ request('type') === 'asset' ? 'selected' : '' }}>🏛️ Imobilizado / Patrimônio</option>
            </select>
            <select name="stock_filter" class="filter-select">
                <option value="">Estoque: Todos</option>
                <option value="low" {{ request('stock_filter') === 'low' ? 'selected' : '' }}>⚠️ Estoque Baixo</option>
                <option value="out" {{ request('stock_filter') === 'out' ? 'selected' : '' }}>❌ Esgotados</option>
            </select>
            <select name="status" class="filter-select">
                <option value="">Todos os status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
            </select>
            <button type="submit" class="btn btn-outline">🔍 Filtrar</button>
        </form>

        @if($products->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Item</th>
                        <th>Tipo</th>
                        <th>Categoria</th>
                        <th>Estoque / Quantidade</th>
                        <th>Valor</th>
                        <th>Canais</th>
                        <th>Status</th>
                        <th>Destaque</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>
                            <div class="table-image">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                @else
                                    <span class="table-placeholder">{{ $product->isAsset() ? '🏛️' : ($product->category->icon ?? '📦') }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <strong>{{ $product->name }}</strong>
                        </td>
                        <td>
                            @if($product->isAsset())
                                <span class="status-badge" style="background: #fef3c7; color: #b45309; font-weight: bold;">🏛️ Imobilizado</span>
                            @elseif($product->isService())
                                <span class="status-badge" style="background: #e0e7ff; color: #4338ca;">🔧 Serviço</span>
                            @else
                                <span class="status-badge" style="background: #f1f5f9; color: #334155;">📦 Produto</span>
                            @endif
                        </td>
                        <td>{{ $product->category->icon ?? '' }} {{ $product->category->name ?? 'N/A' }}</td>
                        <td>
                            @if($product->isService())
                                <span style="color: var(--text-muted); font-size: 0.85rem;">Infinito / Serviço</span>
                            @elseif($product->isAsset())
                                <span class="status-badge" style="background: #e2e8f0; color: #334155; font-weight: bold;">🏛️ {{ $product->stock_quantity }} bens</span>
                            @elseif(!$product->manage_stock)
                                <span style="color: var(--text-muted); font-size: 0.85rem;">Não controlado</span>
                            @else
                                @if($product->isOutOfStock())
                                    <span class="status-badge" style="background: #fee2e2; color: #dc2626; font-weight: bold;">❌ 0 unid (Esgotado)</span>
                                @elseif($product->isLowStock())
                                    <span class="status-badge" style="background: #fef3c7; color: #b45309; font-weight: bold;">⚠️ {{ $product->stock_quantity }} unid (Baixo)</span>
                                @else
                                    <span class="status-badge" style="background: #dcfce7; color: #15803d; font-weight: bold;">✅ {{ $product->stock_quantity }} unid</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($product->promotional_price)
                                <span class="price-old-sm">{{ $product->formatted_price }}</span><br>
                                <span class="price-promo-sm">{{ $product->formatted_promotional_price }}</span>
                            @else
                                {{ $product->formatted_price }}
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                @if($product->isAsset())
                                    <span style="font-size: 0.75rem; background: #fef3c7; color: #92400e; padding: 2px 6px; border-radius: 4px;" title="Uso interno da empresa">🔒 Uso Interno</span>
                                @else
                                    @if($product->show_in_catalog)
                                        <span style="font-size: 0.75rem; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px;" title="Visível no Catálogo Online">🌐 Catálogo</span>
                                    @endif
                                    @if($product->allow_physical_sale)
                                        <span style="font-size: 0.75rem; background: #dcfce7; color: #15803d; padding: 2px 6px; border-radius: 4px;" title="Disponível para venda de balcão">🛒 Balcão</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td>
                            <button class="status-badge {{ $product->is_active ? 'active' : 'inactive' }}"
                                    onclick="toggleStatus({{ $product->id }}, 'active', this)"
                                    title="Clique para {{ $product->is_active ? 'desativar' : 'ativar' }}">
                                {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                            </button>
                        </td>
                        <td>
                            <button class="status-badge {{ $product->is_featured ? 'featured' : 'not-featured' }}"
                                    onclick="toggleStatus({{ $product->id }}, 'featured', this)"
                                    title="Clique para {{ $product->is_featured ? 'remover destaque' : 'destacar' }}">
                                {{ $product->is_featured ? '⭐ Sim' : 'Não' }}
                            </button>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.produtos.edit', $product) }}" class="btn btn-sm btn-outline" title="Editar">✏️</a>
                                <form method="POST" action="{{ route('admin.produtos.destroy', $product) }}" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
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

        <div class="pagination-wrapper">
            {{ $products->withQueryString()->links() }}
        </div>
        @else
        <div class="empty-state-admin">
            <span class="empty-icon">📦</span>
            <h3>Nenhum produto cadastrado</h3>
            <p>Adicione seus primeiros produtos para que apareçam no catálogo e no estoque.</p>
            <a href="{{ route('admin.produtos.create') }}" class="btn btn-primary">+ Novo Produto</a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleStatus(productId, type, btn) {
    const url = type === 'active'
        ? `/admin/produtos/${productId}/toggle-active`
        : `/admin/produtos/${productId}/toggle-featured`;

    fetch(url, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (type === 'active') {
            btn.className = 'status-badge ' + (data.is_active ? 'active' : 'inactive');
            btn.textContent = data.is_active ? 'Ativo' : 'Inativo';
        } else {
            btn.className = 'status-badge ' + (data.is_featured ? 'featured' : 'not-featured');
            btn.textContent = data.is_featured ? '⭐ Sim' : 'Não';
        }
    });
}
</script>
@endsection
