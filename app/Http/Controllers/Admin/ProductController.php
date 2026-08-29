<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('stock_filter')) {
            if ($request->stock_filter === 'low') {
                $query->where('manage_stock', true)->where('type', 'product')->whereColumn('stock_quantity', '<=', 'min_stock_alert');
            } elseif ($request->stock_filter === 'out') {
                $query->where('manage_stock', true)->where('type', 'product')->where('stock_quantity', '<=', 0);
            }
        }

        $products = $query->orderBy('order')->paginate(15);
        $categories = Category::orderBy('order')->get();

        $totalProductsCount = Product::where('type', 'product')->count();
        $totalServicesCount = Product::where('type', 'service')->count();
        $totalAssetsCount = Product::where('type', 'asset')->count();
        $lowStockCount = Product::where('manage_stock', true)->where('type', 'product')->whereColumn('stock_quantity', '<=', 'min_stock_alert')->count();

        return view('admin.products.index', compact('products', 'categories', 'totalProductsCount', 'totalServicesCount', 'totalAssetsCount', 'lowStockCount'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('order')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:product,service,asset',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'promotional_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'nullable|integer|min:0',
            'manage_stock' => 'boolean',
            'min_stock_alert' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
            'show_in_catalog' => 'boolean',
            'allow_physical_sale' => 'boolean',
            'is_featured' => 'boolean',
            'whatsapp_message' => 'nullable|string|max:500',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $tenantId = session('tenant_id');
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['show_in_catalog'] = $request->has('show_in_catalog') && $validated['type'] !== 'asset';
        $validated['allow_physical_sale'] = $request->has('allow_physical_sale') && $validated['type'] !== 'asset';
        $validated['is_featured'] = $request->has('is_featured') && $validated['show_in_catalog'];
        $validated['manage_stock'] = $request->has('manage_stock') || $validated['type'] === 'asset';
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        $validated['min_stock_alert'] = $validated['min_stock_alert'] ?? 2;
        $validated['order'] = Product::max('order') + 1;

        // Upload main image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store("tenants/{$tenantId}/products", 'public');
        }

        $product = Product::create($validated);

        // Upload additional images
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $index => $image) {
                $path = $image->store("tenants/{$tenantId}/products", 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.produtos.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    public function edit(Product $produto)
    {
        $produto->load('images');
        $categories = Category::where('is_active', true)->orderBy('order')->get();
        return view('admin.products.edit', ['product' => $produto, 'categories' => $categories]);
    }

    public function update(Request $request, Product $produto)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:product,service,asset',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'promotional_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'manage_stock' => 'boolean',
            'min_stock_alert' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
            'show_in_catalog' => 'boolean',
            'allow_physical_sale' => 'boolean',
            'is_featured' => 'boolean',
            'whatsapp_message' => 'nullable|string|max:500',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $tenantId = session('tenant_id');
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['show_in_catalog'] = $request->has('show_in_catalog') && $validated['type'] !== 'asset';
        $validated['allow_physical_sale'] = $request->has('allow_physical_sale') && $validated['type'] !== 'asset';
        $validated['is_featured'] = $request->has('is_featured') && $validated['show_in_catalog'];
        $validated['manage_stock'] = $request->has('manage_stock') || $validated['type'] === 'asset';
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        $validated['min_stock_alert'] = $validated['min_stock_alert'] ?? 2;

        // Upload new main image
        if ($request->hasFile('image')) {
            // Delete old image
            if ($produto->image) {
                Storage::disk('public')->delete($produto->image);
            }
            $validated['image'] = $request->file('image')->store("tenants/{$tenantId}/products", 'public');
        }

        $produto->update($validated);

        // Upload additional images
        if ($request->hasFile('additional_images')) {
            $lastOrder = $produto->images()->max('order') ?? -1;
            foreach ($request->file('additional_images') as $index => $image) {
                $path = $image->store("tenants/{$tenantId}/products", 'public');
                ProductImage::create([
                    'product_id' => $produto->id,
                    'image_path' => $path,
                    'order' => $lastOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.produtos.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $produto)
    {
        // Delete images from storage
        if ($produto->image) {
            Storage::disk('public')->delete($produto->image);
        }

        foreach ($produto->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $produto->delete();

        return redirect()->route('admin.produtos.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    public function deleteImage(ProductImage $image)
    {
        $tenantId = session('tenant_id');
        if ($image->product->tenant_id !== $tenantId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true]);
    }

    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $product->is_active,
        ]);
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);

        return response()->json([
            'success' => true,
            'is_featured' => $product->is_featured,
        ]);
    }
}
