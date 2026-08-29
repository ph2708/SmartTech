<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ServiceOrderController;
use App\Http\Controllers\Admin\FinancialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\StoreSettingsController;
use App\Http\Controllers\SuperAdmin\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Catálogo Público
|--------------------------------------------------------------------------
*/
Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/loja/{slug}', [CatalogController::class, 'store'])->name('catalog.store');
Route::get('/loja/{slug}/categoria/{categorySlug}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/loja/{slug}/produto/{productSlug}', [CatalogController::class, 'product'])->name('catalog.product');
Route::get('/loja/{slug}/busca', [CatalogController::class, 'search'])->name('catalog.search');
Route::post('/api/track-click', [CatalogController::class, 'trackClick'])->name('catalog.trackClick');

/*
|--------------------------------------------------------------------------
| Assistente de Implantação / Setup Web (HostGator / cPanel)
|--------------------------------------------------------------------------
*/
Route::get('/install', [App\Http\Controllers\InstallController::class, 'index'])->name('install.index');
Route::post('/install/run', [App\Http\Controllers\InstallController::class, 'run'])->name('install.run');

/*
|--------------------------------------------------------------------------
| Autenticação
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Painel Admin (Lojista)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'identify.tenant'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categorias
    Route::resource('categorias', CategoryController::class);
    Route::post('/categorias/reordenar', [CategoryController::class, 'updateOrder'])->name('categorias.reorder');

    // Produtos
    Route::resource('produtos', ProductController::class);
    Route::delete('/produtos/imagem/{image}', [ProductController::class, 'deleteImage'])->name('produtos.deleteImage');
    Route::patch('/produtos/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('produtos.toggleActive');
    Route::patch('/produtos/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('produtos.toggleFeatured');

    // Vendas e Pedidos (Controle Financeiro)
    Route::resource('pedidos', OrderController::class);

    // Assistência Técnica - Ordens de Serviço (OS)
    Route::get('/ordens-servico/{order}/imprimir', [ServiceOrderController::class, 'print'])->name('ordens-servico.print');
    Route::resource('ordens-servico', ServiceOrderController::class);

    // Controle Financeiro Completo (Fluxo de Caixa, Entradas e Despesas)
    Route::resource('financeiro', FinancialController::class);

    // Gestão de Equipe & Usuários da Loja
    Route::patch('/usuarios/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('usuarios.toggleActive');
    Route::resource('usuarios', UserController::class);

    // Gestão de Filiais / Unidades
    Route::get('/filiais', [BranchController::class, 'index'])->name('filiais.index');
    Route::get('/filiais/criar', [BranchController::class, 'create'])->name('filiais.create');
    Route::post('/filiais', [BranchController::class, 'store'])->name('filiais.store');
    Route::get('/filiais/{branch}/alternar', [BranchController::class, 'switchBranch'])->name('filiais.switch');

    // Emissão Fiscal Focus NFe (NF-e / NFC-e / NFS-e)
    Route::get('/notas-fiscais', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/pedidos/{order}/emitir-nfe', [InvoiceController::class, 'emit'])->name('invoices.emit');
    Route::post('/ordens-servico/{serviceOrder}/emitir-nfse', [InvoiceController::class, 'emitServiceOrder'])->name('invoices.emitServiceOrder');
    Route::post('/notas-fiscais/{invoice}/sincronizar', [InvoiceController::class, 'sync'])->name('invoices.sync');

    // Configurações
    Route::get('/configuracoes', [StoreSettingsController::class, 'edit'])->name('configuracoes.edit');
    Route::put('/configuracoes', [StoreSettingsController::class, 'update'])->name('configuracoes.update');
    Route::post('/configuracoes/test-smtp', [StoreSettingsController::class, 'testSmtp'])->name('configuracoes.testSmtp');
});

/*
|--------------------------------------------------------------------------
| Super Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('superadmin.')->group(function () {
    Route::resource('tenants', TenantController::class);
    Route::patch('/tenants/{tenant}/toggle-active', [TenantController::class, 'toggleActive'])->name('tenants.toggleActive');
});
