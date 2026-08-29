<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $tenant = $tenantId ? \App\Models\Tenant::find($tenantId) : \App\Models\Tenant::first();

        if (!$tenant) {
            $tenant = \App\Models\Tenant::firstOrCreate(
                ['slug' => 'smarttech'],
                ['name' => 'Smart Tech', 'whatsapp' => '64992495817']
            );
        }

        session(['tenant_id' => $tenant->id, 'tenant' => $tenant]);

        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $totalCategories = Category::count();
        $featuredProducts = Product::where('is_featured', true)->count();

        // Métricas de Visitantes e Entradas
        $totalPageViews = \App\Models\AnalyticsEvent::where('type', 'page_view')->count();
        $todayPageViews = \App\Models\AnalyticsEvent::where('type', 'page_view')->whereDate('date', now()->toDateString())->count();
        $totalWhatsAppClicks = \App\Models\AnalyticsEvent::where('type', 'whatsapp_click')->count();
        $todayWhatsAppClicks = \App\Models\AnalyticsEvent::where('type', 'whatsapp_click')->whereDate('date', now()->toDateString())->count();

        // Métricas Financeiras Completas (Entradas, Despesas e Lucro Líquido)
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $monthIncome = \App\Models\FinancialTransaction::where('type', 'income')->where('status', 'pago')->whereYear('date', $currentYear)->whereMonth('date', $currentMonth)->sum('amount');
        $monthExpense = \App\Models\FinancialTransaction::where('type', 'expense')->where('status', 'pago')->whereYear('date', $currentYear)->whereMonth('date', $currentMonth)->sum('amount');
        
        // Se ainda não houver lançamentos no financeiro, soma as vendas e OS concluídas como fallback
        if ($monthIncome == 0) {
            $monthIncome = \App\Models\Order::where('status', 'concluido')->whereMonth('date', $currentMonth)->sum('amount') 
                         + \App\Models\ServiceOrder::where('status', 'entregue')->whereMonth('entry_date', $currentMonth)->sum('final_amount');
        }

        $monthNetProfit = $monthIncome - $monthExpense;
        $totalSalesCount = \App\Models\Order::where('status', 'concluido')->count();
        $recentOrders = \App\Models\Order::with('product')->latest('date')->latest('id')->take(5)->get();

        // Métricas de Ordens de Serviço (OS - Assistência Técnica)
        $totalOsCount = \App\Models\ServiceOrder::count();
        $activeOsCount = \App\Models\ServiceOrder::whereIn('status', ['orcamento', 'aguardando_peca', 'aprovado'])->count();
        $readyOsCount = \App\Models\ServiceOrder::where('status', 'pronto')->count();
        $recentOs = \App\Models\ServiceOrder::latest('entry_date')->latest('id')->take(5)->get();

        // Alerta de Estoque Baixo
        $lowStockProductsCount = Product::where('manage_stock', true)->where('type', 'product')->whereColumn('stock_quantity', '<=', 'min_stock_alert')->count();

        // Métricas de Filiais (quando a loja possui filiais cadastradas)
        $mainStore = $tenant->is_branch && $tenant->parent_id ? $tenant->parent : $tenant;
        $branches = $mainStore ? $mainStore->branches()->where('is_active', true)->get() : collect();
        $hasBranches = $branches->count() > 0;
        $branchesSales = collect();

        if ($hasBranches) {
            // Unidades da rede (Matriz + Filiais)
            $allUnits = collect([$mainStore])->merge($branches);

            $branchesSales = $allUnits->map(function ($unit) use ($currentMonth, $currentYear, $tenant) {
                // Faturamento de vendas
                $salesAmount = \App\Models\Order::withoutGlobalScope('tenant')
                    ->where('tenant_id', $unit->id)
                    ->where('status', 'concluido')
                    ->whereMonth('date', $currentMonth)
                    ->whereYear('date', $currentYear)
                    ->sum('amount');

                // Faturamento de OS concluídas
                $osAmount = \App\Models\ServiceOrder::withoutGlobalScope('tenant')
                    ->where('tenant_id', $unit->id)
                    ->where('status', 'entregue')
                    ->whereMonth('entry_date', $currentMonth)
                    ->whereYear('entry_date', $currentYear)
                    ->sum('final_amount');

                $totalFaturado = $salesAmount + $osAmount;
                $totalVendasQtd = \App\Models\Order::withoutGlobalScope('tenant')
                    ->where('tenant_id', $unit->id)
                    ->where('status', 'concluido')
                    ->whereMonth('date', $currentMonth)
                    ->whereYear('date', $currentYear)
                    ->count();

                $totalOsQtd = \App\Models\ServiceOrder::withoutGlobalScope('tenant')
                    ->where('tenant_id', $unit->id)
                    ->where('status', 'entregue')
                    ->whereMonth('entry_date', $currentMonth)
                    ->whereYear('entry_date', $currentYear)
                    ->count();

                return [
                    'id' => $unit->id,
                    'name' => $unit->is_branch ? ($unit->branch_name ?? $unit->name) : $unit->name . ' (Matriz)',
                    'is_current' => $tenant->id === $unit->id,
                    'sales_amount' => $salesAmount,
                    'os_amount' => $osAmount,
                    'total_revenue' => $totalFaturado,
                    'orders_count' => $totalVendasQtd,
                    'os_count' => $totalOsQtd,
                ];
            });
        }

        return view('admin.dashboard', compact(
            'tenant',
            'totalProducts',
            'activeProducts',
            'totalCategories',
            'featuredProducts',
            'totalPageViews',
            'todayPageViews',
            'totalWhatsAppClicks',
            'todayWhatsAppClicks',
            'monthIncome',
            'monthExpense',
            'monthNetProfit',
            'totalSalesCount',
            'recentOrders',
            'totalOsCount',
            'activeOsCount',
            'readyOsCount',
            'recentOs',
            'lowStockProductsCount',
            'hasBranches',
            'branchesSales'
        ));
    }
}
