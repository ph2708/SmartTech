<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function home()
    {
        // Verifica no banco de dados primeiro ou usa o fallback do .env
        $singleStoreSetting = \App\Models\SystemSetting::get('single_store_mode');
        $isSingleStore = $singleStoreSetting !== null ? filter_var($singleStoreSetting, FILTER_VALIDATE_BOOLEAN) : config('app.single_store_mode', false);

        if ($isSingleStore) {
            $defaultSlug = \App\Models\SystemSetting::get('default_store_slug') ?: config('app.default_store_slug', 'smarttech');
            return $this->store($defaultSlug);
        }

        $tenants = Tenant::where('is_active', true)->where('is_branch', false)->latest()->take(12)->get();
        return view('catalog.home', compact('tenants'));
    }

    public function store(string $slug)
    {
        $tenant = Tenant::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // Registrar visita no catálogo
        \App\Models\AnalyticsEvent::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'type' => 'page_view',
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 255),
            'date' => now()->toDateString(),
        ]);

        $categories = Category::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->withoutGlobalScope('tenant')
                    ->where('is_active', true)
                    ->where('show_in_catalog', true)
                    ->where('type', '!=', 'asset');
            }])
            ->orderBy('order')
            ->get();

        $featuredProducts = Product::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('show_in_catalog', true)
            ->where('type', '!=', 'asset')
            ->where('is_featured', true)
            ->with('category')
            ->orderBy('order')
            ->take(8)
            ->get();

        $allProducts = Product::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('show_in_catalog', true)
            ->where('type', '!=', 'asset')
            ->with('category')
            ->orderBy('order')
            ->get();

        return view('catalog.store', compact('tenant', 'categories', 'featuredProducts', 'allProducts'));
    }

    public function category(string $slug, string $categorySlug)
    {
        $tenant = Tenant::where('slug', $slug)->where('is_active', true)->firstOrFail();

        \App\Models\AnalyticsEvent::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'type' => 'page_view',
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 255),
            'date' => now()->toDateString(),
        ]);

        $category = Category::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = Product::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->where('show_in_catalog', true)
            ->where('type', '!=', 'asset')
            ->orderBy('order')
            ->get();

        $categories = Category::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('catalog.category', compact('tenant', 'category', 'products', 'categories'));
    }

    public function product(string $slug, string $productSlug)
    {
        $tenant = Tenant::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $product = Product::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('slug', $productSlug)
            ->where('is_active', true)
            ->where('show_in_catalog', true)
            ->where('type', '!=', 'asset')
            ->with(['category', 'images'])
            ->firstOrFail();

        \App\Models\AnalyticsEvent::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'type' => 'page_view',
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 255),
            'date' => now()->toDateString(),
        ]);

        $relatedProducts = Product::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('show_in_catalog', true)
            ->where('type', '!=', 'asset')
            ->take(4)
            ->get();

        return view('catalog.product', compact('tenant', 'product', 'relatedProducts'));
    }

    public function trackClick(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'product_id' => 'nullable|exists:products,id',
        ]);

        \App\Models\AnalyticsEvent::withoutGlobalScope('tenant')->create([
            'tenant_id' => $validated['tenant_id'],
            'product_id' => $validated['product_id'] ?? null,
            'type' => 'whatsapp_click',
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 255),
            'date' => now()->toDateString(),
        ]);

        return response()->json(['success' => true]);
    }

    public function search(string $slug, Request $request)
    {
        $tenant = Tenant::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $query = $request->input('q', '');

        $products = Product::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('category')
            ->orderBy('name')
            ->get();

        $categories = Category::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('catalog.search', compact('tenant', 'products', 'categories', 'query'));
    }
}
