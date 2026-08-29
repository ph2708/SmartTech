@extends('layouts.catalog')

@section('title', 'Busca: ' . $query)

@section('content')
<div class="container">
    <section class="section">
        <div class="section-header">
            <h2>🔍 Resultados para "{{ $query }}"</h2>
            <span class="results-count">{{ $products->count() }} {{ $products->count() === 1 ? 'produto encontrado' : 'produtos encontrados' }}</span>
        </div>

        @if($products->count() > 0)
        <div class="products-grid">
            @foreach($products as $product)
                @include('catalog.partials.product-card', ['product' => $product])
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <span class="empty-icon">🔍</span>
            <h3>Nenhum produto encontrado</h3>
            <p>Tente buscar com outros termos ou navegue pelas categorias.</p>
            <a href="{{ route('catalog.store', $tenant->slug) }}" class="btn btn-primary">← Voltar ao catálogo</a>
        </div>
        @endif
    </section>
</div>
@endsection
