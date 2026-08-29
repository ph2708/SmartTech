@extends('layouts.catalog')

@section('title', $product->name)

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="{{ route('catalog.store', $tenant->slug) }}">Início</a>
        <span>›</span>
        <a href="{{ route('catalog.category', [$tenant->slug, $product->category->slug]) }}">{{ $product->category->name }}</a>
        <span>›</span>
        <span>{{ $product->name }}</span>
    </div>

    <div class="product-detail">
        <div class="product-gallery">
            <div class="main-image">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" id="mainImage">
                @else
                    <div class="product-placeholder large">
                        <span>{{ $product->category->icon ?? '📦' }}</span>
                    </div>
                @endif

                @if($product->promotional_price)
                <div class="discount-badge large">-{{ $product->discount_percent }}%</div>
                @endif
            </div>

            @if($product->images->count() > 0)
            <div class="thumbnail-list">
                @if($product->image_url)
                <button class="thumbnail active" onclick="changeImage('{{ $product->image_url }}', this)">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                </button>
                @endif
                @foreach($product->images as $img)
                <button class="thumbnail" onclick="changeImage('{{ $img->image_url }}', this)">
                    <img src="{{ $img->image_url }}" alt="{{ $product->name }}">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        <div class="product-info-detail">
            <span class="product-category-tag">{{ $product->category->icon }} {{ $product->category->name }}</span>
            <h1>{{ $product->name }}</h1>

            <div class="product-pricing-detail">
                @if($product->promotional_price)
                    <span class="price-old-detail">De: {{ $product->formatted_price }}</span>
                    <span class="price-current-detail">{{ $product->formatted_promotional_price }}</span>
                    <span class="savings">Você economiza R$ {{ number_format($product->price - $product->promotional_price, 2, ',', '.') }}</span>
                @else
                    <span class="price-current-detail">{{ $product->formatted_price }}</span>
                @endif
            </div>

            @if($product->description)
            <div class="product-description">
                <h3>Descrição</h3>
                <p>{{ $product->description }}</p>
            </div>
            @endif

            <a href="{{ $product->whatsapp_url }}" target="_blank" class="btn-whatsapp-large">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Comprar pelo WhatsApp
            </a>

            <div class="share-section">
                <span>Compartilhar:</span>
                <button onclick="navigator.clipboard.writeText(window.location.href); this.textContent='✅ Copiado!'" class="share-btn">📋 Copiar link</button>
                <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . ($product->promotional_price ? $product->formatted_promotional_price : $product->formatted_price) . ' ' . url()->current()) }}" target="_blank" class="share-btn">💬 WhatsApp</a>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <section class="section">
        <div class="section-header">
            <h2>Produtos Relacionados</h2>
        </div>
        <div class="products-grid">
            @foreach($relatedProducts as $related)
                @include('catalog.partials.product-card', ['product' => $related])
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection

@section('scripts')
<script>
function changeImage(src, el) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}
</script>
@endsection
