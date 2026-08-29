<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $tenant->description ?? 'Catálogo de produtos' }}">
    <title>{{ $tenant->name ?? 'Catálogo' }} - @yield('title', 'Produtos')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/catalog.css') }}">
    <style>
        :root {
            --primary: {{ $tenant->primary_color ?? '#e63946' }};
            --secondary: {{ $tenant->secondary_color ?? '#1d1d1d' }};
            --primary-light: {{ $tenant->primary_color ?? '#e63946' }}22;
            --primary-hover: {{ $tenant->primary_color ?? '#e63946' }}dd;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Top Admin Bar (Apenas se o usuário estiver autenticado) -->
    @auth
    <div style="background: #0f172a; color: #f8fafc; padding: 8px 0; font-size: 0.85rem; border-bottom: 1px solid #334155; position: relative; z-index: 101;">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 0.75rem;">MODO ADMIN</span>
                <span>Logado como: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role_label }})</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <a href="{{ route('admin.dashboard') }}" style="background: var(--primary, #e63946); color: white; padding: 5px 12px; border-radius: 6px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                    📊 Ir para o Painel Admin →
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0; display: inline;">
                    @csrf
                    <button type="submit" style="background: transparent; color: #f87171; border: 1px solid #7f1d1d; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;" title="Encerrar Sessão">
                        🚪 Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endauth

    <!-- Header -->
    <header class="catalog-header">
        <div class="container">
            <div class="header-content">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <a href="{{ config('app.single_store_mode') ? url('/') : route('catalog.store', $tenant->slug) }}" class="store-logo">
                        @if($tenant->logo_url)
                            <img src="{{ $tenant->logo_url }}" alt="{{ $tenant->name }}">
                        @else
                            <div class="store-logo-text">
                                <span class="logo-icon">🏪</span>
                                <h1>{{ $tenant->name }}</h1>
                            </div>
                        @endif
                    </a>

                    <!-- Seletor de Filiais / Unidades para o Cliente (Apenas se houver mais de uma unidade na rede) -->
                    @php
                        $mainStore = ($tenant->is_branch && $tenant->parent_id) ? \App\Models\Tenant::find($tenant->parent_id) : $tenant;
                        $storeBranches = $mainStore ? \App\Models\Tenant::where('parent_id', $mainStore->id)->where('is_active', true)->get() : collect();
                        $hasMultipleUnits = $storeBranches->count() > 0;
                    @endphp
                    @if($hasMultipleUnits)
                    <div class="branch-selector-dropdown" style="position: relative; margin-left: 8px;">
                        <button type="button" onclick="toggleBranchDropdown(event)" id="branchDropdownBtn" style="background: rgba(255,255,255,0.18); color: white; border: 1px solid rgba(255,255,255,0.35); padding: 7px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
                            <span>📍 {{ $tenant->is_branch ? ($tenant->branch_name ?? $tenant->name) : 'Matriz' }}</span>
                            <span style="font-size: 0.65rem; opacity: 0.85;">▼</span>
                        </button>
                        <div id="branchMenu" class="branch-dropdown-menu" style="display: none; position: absolute; top: calc(100% + 8px); left: 0; background: #ffffff; color: #1e293b; border-radius: 12px; box-shadow: 0 12px 35px rgba(0,0,0,0.25); min-width: 260px; z-index: 9999; padding: 6px 0; border: 1px solid #e2e8f0;">
                            <div style="padding: 10px 16px; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 800; border-bottom: 1px solid #f1f5f9; letter-spacing: 0.5px;">
                                🏪 Selecione uma unidade:
                            </div>
                            <a href="{{ route('catalog.store', $mainStore->slug) }}" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; color: #0f172a; text-decoration: none; font-size: 0.9rem; transition: background 0.2s; {{ $tenant->id === $mainStore->id ? 'background: #f1f5f9; font-weight: 800; color: #e63946;' : '' }}">
                                <span>👑 {{ $mainStore->name }} (Matriz)</span>
                                @if($tenant->id === $mainStore->id)<span style="color: #10b981; font-weight: bold;">✓ Atual</span>@endif
                            </a>
                            @foreach($storeBranches as $b)
                            <a href="{{ route('catalog.store', $b->slug) }}" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; color: #0f172a; text-decoration: none; font-size: 0.9rem; transition: background 0.2s; {{ $tenant->id === $b->id ? 'background: #f1f5f9; font-weight: 800; color: #e63946;' : '' }}">
                                <span>🏢 {{ $b->branch_name ?? $b->name }}</span>
                                @if($tenant->id === $b->id)<span style="color: #10b981; font-weight: bold;">✓ Atual</span>@endif
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <form action="{{ route('catalog.search', $tenant->slug) }}" method="GET" class="search-form">
                    <input type="text" name="q" placeholder="Buscar produtos..." value="{{ request('q') }}">
                    <button type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                </form>

                <div style="display: flex; align-items: center; gap: 10px;">
                    @auth
                    <a href="{{ route('admin.dashboard') }}" class="header-admin-btn" style="background: rgba(255,255,255,0.15); color: white; padding: 10px 16px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; border: 1px solid rgba(255,255,255,0.2);">
                        <span>⚙️ Painel</span>
                    </a>
                    @endauth

                    @if($tenant->show_instagram && $tenant->instagram_url)
                    <a href="{{ $tenant->instagram_url }}" target="_blank" class="header-instagram" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: white; padding: 10px 14px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: transform 0.2s;" title="Siga-nos no Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        <span>Instagram</span>
                    </a>
                    @endif

                    <a href="{{ $tenant->whatsapp_link }}" target="_blank" class="header-whatsapp">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <span>WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Categories Nav -->
    @if(isset($categories) && $categories->count() > 0)
    <nav class="categories-nav">
        <div class="container">
            <div class="categories-scroll">
                <a href="{{ route('catalog.store', $tenant->slug) }}" class="cat-pill {{ !isset($category) ? 'active' : '' }}">
                    🏠 Todos
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('catalog.category', [$tenant->slug, $cat->slug]) }}" class="cat-pill {{ isset($category) && $category->id === $cat->id ? 'active' : '' }}">
                    {{ $cat->icon }} {{ $cat->name }}
                </a>
                @endforeach
            </div>
        </div>
    </nav>
    @endif

    <!-- Main Content -->
    <main class="catalog-main">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="catalog-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <h3>{{ $tenant->name }}</h3>
                    @if($tenant->description)
                        <p>{{ $tenant->description }}</p>
                    @endif
                    @if($tenant->address)
                        <p>📍 {{ $tenant->address }}{{ $tenant->city ? ', ' . $tenant->city : '' }}{{ $tenant->state ? ' - ' . $tenant->state : '' }}</p>
                    @endif
                </div>
                <div class="footer-social">
                    <a href="{{ $tenant->whatsapp_link }}" target="_blank" class="social-link whatsapp">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                    @if($tenant->show_instagram && $tenant->instagram_url)
                    <a href="{{ $tenant->instagram_url }}" target="_blank" class="social-link instagram">📸 Instagram</a>
                    @endif
                    @if($tenant->facebook)
                    <a href="{{ $tenant->facebook }}" target="_blank" class="social-link facebook">📘 Facebook</a>
                    @endif
                </div>
            </div>
            <div class="footer-bottom">
                <p>© {{ date('Y') }} {{ $tenant->name }}. Todos os direitos reservados.</p>
                @if(!config('app.single_store_mode'))
                <p class="powered-by">Powered by <a href="{{ route('home') }}">SmartCatálogo</a></p>
                @endif
            </div>
        </div>
    </footer>

    <!-- WhatsApp Float Button -->
    <a href="{{ $tenant->whatsapp_link }}" target="_blank" class="whatsapp-float" title="Fale conosco no WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-whatsapp, .btn-whatsapp-large, .header-whatsapp, .whatsapp-float').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var tenantId = '{{ $tenant->id ?? '' }}';
                var productId = '{{ $product->id ?? '' }}';
                if (tenantId) {
                    navigator.sendBeacon('/api/track-click', new URLSearchParams({
                        tenant_id: tenantId,
                        product_id: productId
                    }));
                }
            });
        });

        // Fechar dropdown de filiais ao clicar fora
        window.addEventListener('click', function(e) {
            var menu = document.getElementById('branchMenu');
            if (menu && !e.target.closest('.branch-selector-dropdown')) {
                menu.style.display = 'none';
            }
        });
    });

    function toggleBranchDropdown(event) {
        if (event) event.stopPropagation();
        var menu = document.getElementById('branchMenu');
        if (menu) {
            menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
        }
    }
    </script>
    @yield('scripts')
</body>
</html>
