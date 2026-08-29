<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SmartCatálogo - Crie seu catálogo online e venda pelo WhatsApp. Plataforma para lojas de assistência técnica, acessórios e muito mais.">
    <title>SmartCatálogo - Seu catálogo online com WhatsApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/catalog.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body class="home-body">
    <!-- Hero -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="container">
            <nav class="home-nav">
                <div class="brand">
                    <span class="brand-icon">⚡</span>
                    <span class="brand-name">SmartCatálogo</span>
                </div>
                <div class="nav-links">
                    <a href="{{ route('login') }}" class="btn btn-outline">Entrar</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Criar Conta Grátis</a>
                </div>
            </nav>

            <div class="hero-content">
                <div class="hero-badge">🚀 Plataforma #1 para catálogos online</div>
                <h1>Seu catálogo online<br>com vendas pelo <span class="gradient-text">WhatsApp</span></h1>
                <p class="hero-subtitle">Crie seu catálogo de produtos em minutos e receba pedidos direto no seu WhatsApp. Perfeito para lojas de assistência técnica, acessórios, perfumes e muito mais.</p>
                <div class="hero-actions">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                        Criar Meu Catálogo Grátis
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat"><strong>{{ $tenants->count() }}+</strong><span>Lojas ativas</span></div>
                    <div class="stat"><strong>WhatsApp</strong><span>Integração direta</span></div>
                    <div class="stat"><strong>100%</strong><span>Gratuito</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Por que escolher o SmartCatálogo?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>WhatsApp Integrado</h3>
                    <p>Cada produto tem um botão que envia mensagem pré-formatada direto pro seu WhatsApp.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Painel Fácil</h3>
                    <p>Cadastre produtos em segundos. Upload de fotos, preços, promoções — tudo num painel intuitivo.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎨</div>
                    <h3>Personalizável</h3>
                    <p>Escolha as cores da sua marca, adicione logo e deixe o catálogo com a cara da sua loja.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Categorias Organizadas</h3>
                    <p>Organize seus produtos em categorias. Assistência técnica, acessórios, perfumes — tudo separado.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🏷️</div>
                    <h3>Promoções</h3>
                    <p>Coloque preço promocional e destaque produtos. Seus clientes veem o desconto na hora.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📲</div>
                    <h3>Mobile First</h3>
                    <p>Catálogo 100% responsivo. Perfeito para seus clientes abrirem direto do celular.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stores Showcase -->
    @if($tenants->count() > 0)
    <section class="stores-showcase">
        <div class="container">
            <h2 class="section-title">Lojas que já usam</h2>
            <div class="stores-grid">
                @foreach($tenants as $tenant)
                <a href="{{ route('catalog.store', $tenant->slug) }}" class="store-card">
                    <div class="store-card-header" style="background: linear-gradient(135deg, {{ $tenant->primary_color }}, {{ $tenant->secondary_color }})">
                        @if($tenant->logo_url)
                            <img src="{{ $tenant->logo_url }}" alt="{{ $tenant->name }}">
                        @else
                            <span class="store-initial">{{ mb_substr($tenant->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="store-card-body">
                        <h3>{{ $tenant->name }}</h3>
                        <p>{{ Str::limit($tenant->description, 60) }}</p>
                        <span class="visit-link">Visitar loja →</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-card">
                <h2>Pronto para vender mais?</h2>
                <p>Crie seu catálogo online em menos de 5 minutos. Totalmente grátis.</p>
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Começar Agora</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="home-footer">
        <div class="container">
            <p>© {{ date('Y') }} SmartCatálogo. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
