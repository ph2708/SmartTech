@extends('layouts.catalog')

@section('title', $category->name)

@section('content')
<div class="container">
    <section class="section">
        <div class="section-header">
            <h2>{{ $category->icon }} {{ $category->name }}</h2>
            <span class="results-count">{{ $products->count() }} {{ $products->count() === 1 ? 'produto' : 'produtos' }}</span>
        </div>

        @if($category->description)
            <p class="section-description">{{ $category->description }}</p>
        @endif

        @if($products->count() > 0)
        <div class="products-grid">
            @foreach($products as $product)
                @include('catalog.partials.product-card', ['product' => $product])
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <span class="empty-icon">{{ $category->icon ?? '📦' }}</span>
            <h3>Nenhum produto nesta categoria</h3>
            <p>Em breve teremos novidades!</p>
            <a href="{{ route('catalog.store', $tenant->slug) }}" class="btn btn-primary">← Voltar ao catálogo</a>
        </div>
        @endif
    </section>
</div>
@endsection
