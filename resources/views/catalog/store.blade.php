@extends('layouts.catalog')

@section('title', 'Catálogo')

@section('content')
<div class="container">
    <!-- Banner Carrossel Interativo & Moderno -->
    <div class="hero-carousel-container" style="margin: 20px 0 35px 0; position: relative; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.12); background: linear-gradient(135deg, var(--secondary, #0f172a) 0%, #1e293b 100%); color: white;">
        <div class="carousel-track" id="heroCarouselTrack" style="display: flex; transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); width: 100%;">
            
            <!-- Slide 1: Apresentação da Loja & WhatsApp -->
            <div class="carousel-slide" style="min-width: 100%; padding: 45px 65px 55px 65px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px; box-sizing: border-box;">
                <div style="flex: 1; min-width: 280px; z-index: 2;">
                    <span style="background: var(--primary, #e63946); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;">⚡ Atendimento Rápido</span>
                    <h2 style="font-size: 2.1rem; font-weight: 800; margin: 12px 0 10px 0; line-height: 1.25; color: #ffffff;">{{ $tenant->name }}</h2>
                    <p style="color: #cbd5e1; font-size: 1rem; margin-bottom: 24px; max-width: 540px; line-height: 1.6;">{{ $tenant->description ?: 'Os melhores produtos, acessórios premium e assistência técnica especializada com garantia.' }}</p>
                    <div style="display: flex; gap: 14px; flex-wrap: wrap; align-items: center;">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tenant->whatsapp) }}?text=Ol%C3%A1!%20Gostaria%20de%20tirar%20uma%20d%C3%BAvida%20sobre%20o%20cat%C3%A1logo" target="_blank" style="background: #25D366; color: white; padding: 12px 24px; border-radius: 12px; font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 4px 15px rgba(37,211,102,0.35); transition: all 0.2s;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            <span>Fale Conosco no WhatsApp</span>
                        </a>
                        @if($tenant->show_instagram && $tenant->instagram_url)
                        <a href="{{ $tenant->instagram_url }}" target="_blank" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: white; padding: 12px 24px; border-radius: 12px; font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 4px 15px rgba(220,39,67,0.35); transition: all 0.2s;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                            <span>Siga no Instagram</span>
                        </a>
                        @endif
                    </div>
                </div>
                <div style="flex: 0 0 160px; text-align: center; z-index: 2;">
                    @if($tenant->logo_url)
                        <img src="{{ $tenant->logo_url }}" alt="{{ $tenant->name }}" style="max-height: 130px; max-width: 170px; object-fit: contain; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.35)); border-radius: 12px;">
                    @else
                        <div style="font-size: 5.5rem;">🏪</div>
                    @endif
                </div>
            </div>

            <!-- Slide 2: Assistência Técnica Especializada -->
            <div class="carousel-slide" style="min-width: 100%; padding: 45px 65px 55px 65px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px; box-sizing: border-box; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                <div style="flex: 1; min-width: 280px; z-index: 2;">
                    <span style="background: #6366f1; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; display: inline-block;">🔧 Assistência & Reparos</span>
                    <h2 style="font-size: 2.1rem; font-weight: 800; margin: 12px 0 10px 0; line-height: 1.25; color: #ffffff;">Seu aparelho quebrou ou parou?</h2>
                    <p style="color: #cbd5e1; font-size: 1rem; margin-bottom: 24px; max-width: 540px; line-height: 1.6;">Orçamento rápido para Celulares, Computadores, Notebooks e Placas com peças originais e garantia de 90 dias.</p>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tenant->whatsapp) }}?text=Ol%C3%A1!%20Preciso%20de%20um%20or%C3%A7amento%20para%20conserto%20de%20aparelho" target="_blank" style="background: #6366f1; color: white; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(99,102,241,0.4);">
                            <span>🛠️ Solicitar Orçamento de Reparo</span>
                        </a>
                    </div>
                </div>
                <div style="flex: 0 0 140px; text-align: center; font-size: 5.5rem; z-index: 2;">📱</div>
            </div>

            <!-- Slide 3: Garantia & Qualidade -->
            <div class="carousel-slide" style="min-width: 100%; padding: 45px 65px 55px 65px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px; box-sizing: border-box; background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);">
                <div style="flex: 1; min-width: 280px; z-index: 2;">
                    <span style="background: #10b981; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; display: inline-block;">🛡️ Qualidade Garantida</span>
                    <h2 style="font-size: 2.1rem; font-weight: 800; margin: 12px 0 10px 0; line-height: 1.25; color: #ffffff;">Acessórios Selecionados</h2>
                    <p style="color: #cbd5e1; font-size: 1rem; margin-bottom: 24px; max-width: 540px; line-height: 1.6;">Películas de alta resistência, cabos reforçados, carregadores turbo homologados e perfumes importados.</p>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                        <a href="#cat-capinhas-peliculas" style="background: #10b981; color: white; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(16,185,129,0.4);">
                            <span>🛒 Ver Produtos Abaixo ↓</span>
                        </a>
                    </div>
                </div>
                <div style="flex: 0 0 140px; text-align: center; font-size: 5.5rem; z-index: 2;">🎧</div>
            </div>

        </div>

        <!-- Controles do Carrossel (Setas e Pontos com Z-Index e posições seguras) -->
        <button onclick="prevSlide()" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); background: rgba(15,23,42,0.65); color: white; border: 1px solid rgba(255,255,255,0.25); width: 42px; height: 42px; border-radius: 50%; cursor: pointer; font-size: 1.4rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(6px); transition: all 0.2s; z-index: 10;" title="Slide Anterior">‹</button>
        <button onclick="nextSlide()" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: rgba(15,23,42,0.65); color: white; border: 1px solid rgba(255,255,255,0.25); width: 42px; height: 42px; border-radius: 50%; cursor: pointer; font-size: 1.4rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(6px); transition: all 0.2s; z-index: 10;" title="Próximo Slide">›</button>

        <div id="carouselDots" style="position: absolute; bottom: 14px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10;">
            <span onclick="goToSlide(0)" class="dot active" style="width: 10px; height: 10px; border-radius: 50%; background: #ffffff; cursor: pointer; transition: all 0.2s;"></span>
            <span onclick="goToSlide(1)" class="dot" style="width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.4); cursor: pointer; transition: all 0.2s;"></span>
            <span onclick="goToSlide(2)" class="dot" style="width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.4); cursor: pointer; transition: all 0.2s;"></span>
        </div>
    </div>

    <!-- Featured Products -->
    @if($featuredProducts->count() > 0)
    <section class="section">
        <div class="section-header">
            <h2>⭐ Destaques da Semana</h2>
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

@section('scripts')
<script>
let currentSlide = 0;
const totalSlides = 3;
let slideInterval;

function updateSlide() {
    const track = document.getElementById('heroCarouselTrack');
    const dots = document.querySelectorAll('#carouselDots .dot');
    if (track) {
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
    }
    dots.forEach((dot, idx) => {
        if (idx === currentSlide) {
            dot.style.background = '#ffffff';
            dot.style.transform = 'scale(1.25)';
        } else {
            dot.style.background = 'rgba(255,255,255,0.4)';
            dot.style.transform = 'scale(1)';
        }
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateSlide();
    resetAutoSlide();
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    updateSlide();
    resetAutoSlide();
}

function goToSlide(index) {
    currentSlide = index;
    updateSlide();
    resetAutoSlide();
}

function resetAutoSlide() {
    clearInterval(slideInterval);
    slideInterval = setInterval(nextSlide, 5000);
}

document.addEventListener('DOMContentLoaded', () => {
    slideInterval = setInterval(nextSlide, 5000);
});
</script>
@endsection
