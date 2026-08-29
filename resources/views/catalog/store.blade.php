@extends('layouts.catalog')

@section('title', 'Catálogo')

@section('content')
<div class="container">
    <!-- Featured Products -->
    @if($featuredProducts->count() > 0)
    <section class="section">
        <div class="section-header">
            <h2>⭐ Destaques</h2>
        </div>
        <div class="products-grid featured-grid">
            @foreach($featuredProducts as $product)
                @include('catalog.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </section>
    @endif

    <!-- Products by Category -->
    @foreach($categories as $cat)
        @php
            $catProducts = $allProducts->where('category_id', $cat->id);
        @endphp
        @if($catProducts->count() > 0)
        <section class="section" id="cat-{{ $cat->slug }}">
            <div class="section-header">
                <h2>{{ $cat->icon }} {{ $cat->name }}</h2>
                <a href="{{ route('catalog.category', [$tenant->slug, $cat->slug]) }}" class="see-all">Ver todos →</a>
            </div>
            <div class="products-grid">
                @foreach($catProducts->take(4) as $product)
                    @include('catalog.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
        @endif
    @endforeach

    @if($allProducts->count() === 0)
    <div class="empty-state">
        <span class="empty-icon">📦</span>
        <h3>Nenhum produto disponível</h3>
        <p>Esta loja ainda não adicionou produtos ao catálogo.</p>
    </div>
    @endif
</div>
@endsection
