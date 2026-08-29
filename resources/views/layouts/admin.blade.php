<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - @yield('title', 'Dashboard') | SmartCatálogo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @yield('styles')
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <span class="logo-icon">⚡</span>
                <span class="logo-text">SmartCatálogo</span>
            </div>
            <button class="sidebar-close" id="sidebarClose">✕</button>
        </div>

        @php 
            $activeTenant = session('tenant') ?? (auth()->user()?->tenant_id ? \App\Models\Tenant::find(auth()->user()->tenant_id) : \App\Models\Tenant::first()); 
        @endphp
        @if($activeTenant)
        <div class="tenant-info" style="{{ $activeTenant->is_branch ? 'border-left: 3px solid #3b82f6; background: rgba(59, 130, 246, 0.08);' : '' }}">
            <div class="tenant-avatar" style="{{ $activeTenant->is_branch ? 'background: #3b82f6;' : '' }}">
                {{ $activeTenant->is_branch ? '🏢' : mb_substr($activeTenant->name ?? 'S', 0, 1) }}
            </div>
            <div class="tenant-details">
                <span class="tenant-name">{{ $activeTenant->is_branch ? ($activeTenant->branch_name ?? $activeTenant->name) : ($activeTenant->name ?? 'Smart Tech') }}</span>
                <span class="tenant-plan" style="{{ $activeTenant->is_branch ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                    {{ $activeTenant->is_branch ? '🏢 Unidade Filial' : '👑 Matriz Principal' }}
                </span>
            </div>
        </div>
        @endif

        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.categorias.index') }}" class="nav-item {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}">
                <span class="nav-icon">📁</span>
                <span>Categorias</span>
            </a>
            <a href="{{ route('admin.produtos.index') }}" class="nav-item {{ request()->routeIs('admin.produtos.*') ? 'active' : '' }}">
                <span class="nav-icon">📦</span>
                <span>Produtos & Estoque</span>
            </a>
            <a href="{{ route('admin.ordens-servico.index') }}" class="nav-item {{ request()->routeIs('admin.ordens-servico.*') ? 'active' : '' }}">
                <span class="nav-icon">🛠️</span>
                <span>Assistência / OS</span>
            </a>
            <a href="{{ route('admin.financeiro.index') }}" class="nav-item {{ request()->routeIs('admin.financeiro.*') ? 'active' : '' }}">
                <span class="nav-icon">💵</span>
                <span>Fluxo de Caixa & Despesas</span>
            </a>
            <a href="{{ route('admin.pedidos.index') }}" class="nav-item {{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}">
                <span class="nav-icon">🛒</span>
                <span>Vendas / Pedidos</span>
            </a>
            <a href="{{ route('admin.invoices.index') }}" class="nav-item {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                <span class="nav-icon">🧾</span>
                <span>Notas Fiscais (NFe)</span>
            </a>
            <a href="{{ route('admin.usuarios.index') }}" class="nav-item {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                <span class="nav-icon">👥</span>
                <span>Usuários & Equipe</span>
            </a>
            <a href="{{ route('admin.filiais.index') }}" class="nav-item {{ request()->routeIs('admin.filiais.*') ? 'active' : '' }}">
                <span class="nav-icon">🏢</span>
                <span>Filiais & Unidades</span>
            </a>
            <a href="{{ route('admin.configuracoes.edit') }}" class="nav-item {{ request()->routeIs('admin.configuracoes.*') ? 'active' : '' }}">
                <span class="nav-icon">⚙️</span>
                <span>Configurações</span>
            </a>

            @if($activeTenant)
            <div class="nav-divider"></div>
            <a href="{{ config('app.single_store_mode') ? url('/') : route('catalog.store', $activeTenant->slug) }}" target="_blank" class="nav-item">
                <span class="nav-icon">🌐</span>
                <span>Ver Catálogo ({{ $activeTenant->is_branch ? ($activeTenant->branch_name ?? $activeTenant->name) : 'Matriz' }})</span>
            </a>
            @endif

            @if(auth()->user()->isSuperAdmin())
            <div class="nav-divider"></div>
            <span class="nav-section-title">Super Admin</span>
            <a href="{{ route('superadmin.tenants.index') }}" class="nav-item {{ request()->routeIs('superadmin.*') ? 'active' : '' }}">
                <span class="nav-icon">🏢</span>
                <span>Gerenciar Lojas</span>
            </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item logout-btn">
                    <span class="nav-icon">🚪</span>
                    <span>Sair</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Top Bar -->
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle">
                <span></span><span></span><span></span>
            </button>
            <h2 class="page-title">@yield('title', 'Dashboard')</h2>
            <div class="topbar-actions" style="display: flex; align-items: center; gap: 14px;">
                <span class="user-name" style="font-size: 0.9rem; color: #475569;">👤 <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role_label }})</span>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline" style="color: #ef4444; border-color: #fca5a5; background: #fff; display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; font-weight: bold;" title="Encerrar Sessão">
                        <span>🚪 Sair</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="alert alert-success" id="flashMessage">
            <span>✅</span> {{ session('success') }}
            <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error" id="flashMessage">
            <span>❌</span> {{ session('error') }}
            <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-error">
            <span>❌</span>
            <ul style="margin:0;padding-left:1rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
        </div>
        @endif

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </div>

    <script>
        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menuToggle');
        const sidebarClose = document.getElementById('sidebarClose');

        menuToggle?.addEventListener('click', () => sidebar.classList.toggle('open'));
        sidebarClose?.addEventListener('click', () => sidebar.classList.remove('open'));

        // Auto-hide flash messages
        setTimeout(() => {
            document.getElementById('flashMessage')?.remove();
        }, 5000);
    </script>
    @yield('scripts')
</body>
</html>
