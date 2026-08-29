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
    <!-- Top Admin Bar (Apenas se o usuário estiver autenticado e não estiver no modo preview forçado) -->
    @php
        $isPreview = request()->has('preview') || session('preview_mode', false);
    @endphp

    @auth
    @if(!$isPreview)
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #f8fafc; padding: 10px 0; font-size: 0.85rem; border-bottom: 2px solid var(--primary, #e63946); position: relative; z-index: 1001; box-shadow: 0 4px 15px rgba(0,0,0,0.25);">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="background: var(--primary, #e63946); color: white; padding: 3px 10px; border-radius: 20px; font-weight: 800; font-size: 0.75rem; letter-spacing: 0.5px; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">MODO ADMIN ATIVO</span>
                <span>Visualizando loja como Administrador: <strong>{{ auth()->user()->name }}</strong></span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <a href="{{ url()->current() }}?preview=1" style="background: rgba(255,255,255,0.12); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.25); padding: 5px 12px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: background 0.2s;" title="Visualizar catálogo exatamente como um cliente comum vê">
                    👁️ Ver Como Cliente (Preview Deslogado)
                </a>
                <a href="{{ route('admin.produtos.create') }}" style="background: #10b981; color: white; padding: 5px 12px; border-radius: 6px; font-weight: bold; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
                    + Novo Item
                </a>
                <a href="{{ route('admin.dashboard') }}" style="background: #3b82f6; color: white; padding: 5px 12px; border-radius: 6px; font-weight: bold; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                    📊 Painel Admin →
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0; display: inline;">
                    @csrf
                    <button type="submit" style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #ef4444; padding: 5px 10px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;" title="Encerrar Sessão">
                        🚪 Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
    @else
    <!-- Barra de aviso quando estiver no Modo Preview de Cliente -->
    <div style="background: #f59e0b; color: #78350f; padding: 8px 0; font-size: 0.85rem; font-weight: 700; border-bottom: 1px solid #d97706; position: sticky; top: 0; z-index: 1002; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span>👁️ <strong>MODO PREVIEW DESLOGADO:</strong> Você está visualizando o catálogo exatamente como um cliente comum!</span>
            </div>
            <a href="{{ strtok(url()->full(), '?') }}" style="background: #78350f; color: white; padding: 4px 12px; border-radius: 6px; font-size: 0.8rem; text-decoration: none; font-weight: 700;">
                ✕ Sair do Preview & Voltar ao Modo Admin
            </a>
        </div>
    </div>
    @endif
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
                    <a href="{{ route('admin.dashboard') }}" class="header-admin-btn" style="background: rgba(255,255,255,0.15); color: white; padding: 9px 16px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; border: 1px solid rgba(255,255,255,0.25); transition: all 0.2s;">
                        <span>⚙️ Painel</span>
                    </a>
                    @endauth

                    @if($tenant->show_instagram && $tenant->instagram_url)
                    <a href="{{ $tenant->instagram_url }}" target="_blank" class="header-instagram" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: white; padding: 9px 18px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; box-shadow: 0 4px 12px rgba(220,39,67,0.35); transition: transform 0.2s; white-space: nowrap;" title="Siga-nos no Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        <span>Instagram</span>
                    </a>
                    @endif

                    <a href="{{ $tenant->whatsapp_link }}" target="_blank" class="header-whatsapp" style="background: #25D366; color: white; padding: 9px 18px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; box-shadow: 0 4px 12px rgba(37,211,102,0.35); transition: transform 0.2s; white-space: nowrap;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
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
            <div class="footer-content" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 35px; align-items: start;">
                <div class="footer-info">
                    <h3 style="font-size: 1.3rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">{{ $tenant->name }}</h3>
                    @if($tenant->description)
                        <p style="color: #cbd5e1; font-size: 0.95rem; margin-bottom: 14px; line-height: 1.6;">{{ $tenant->description }}</p>
                    @endif
                    @if($tenant->address)
                        <p style="color: #e2e8f0; font-size: 0.95rem; display: flex; align-items: flex-start; gap: 8px; margin-bottom: 8px;">
                            <span>📍</span>
                            <span>{{ $tenant->address }}{{ $tenant->city ? ', ' . $tenant->city : '' }}{{ $tenant->state ? ' - ' . $tenant->state : '' }}</span>
                        </p>
                    @endif

                    @if($tenant->google_maps_link)
                    <div style="margin-top: 14px;">
                        <a href="{{ $tenant->google_maps_link }}" target="_blank" style="background: rgba(255,255,255,0.12); color: #ffffff; border: 1px solid rgba(255,255,255,0.25); padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s;">
                            <span>🗺️ Abrir no Google Maps / GPS</span>
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Mapa Incorporado (Google Maps) -->
                @if($tenant->google_maps_embed)
                <div class="footer-map" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.15); max-height: 200px;">
                    <div style="position: relative; width: 100%; height: 180px; overflow: hidden;">
                        {!! $tenant->google_maps_embed !!}
                    </div>
                </div>
                @endif

                <div class="footer-social" style="display: flex; flex-direction: column; gap: 10px;">
                    <span style="font-weight: 700; color: #ffffff; font-size: 0.95rem; margin-bottom: 4px;">Canais de Atendimento:</span>
                    <a href="{{ $tenant->whatsapp_link }}" target="_blank" class="social-link whatsapp" style="background: #25D366; color: white; padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 10px rgba(37,211,102,0.3);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <span>Falar no WhatsApp</span>
                    </a>
                    @if($tenant->show_instagram && $tenant->instagram_url)
                    <a href="{{ $tenant->instagram_url }}" target="_blank" class="social-link instagram" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: white; padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 10px rgba(220,39,67,0.3);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        <span>Instagram Oficial</span>
                    </a>
                    @endif
                    @if($tenant->facebook)
                    <a href="{{ $tenant->facebook }}" target="_blank" class="social-link facebook" style="background: #1877f2; color: white; padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <span>📘 Facebook</span>
                    </a>
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
