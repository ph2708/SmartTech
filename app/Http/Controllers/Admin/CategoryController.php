<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = Category::max('order') + 1;

        Category::create($validated);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(Category $categoria)
    {
        return view('admin.categories.edit', ['category' => $categoria]);
    }

    public function update(Request $request, Category $categoria)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $categoria->update($validated);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $categoria)
    {
        if ($categoria->products()->count() > 0) {
            return redirect()->route('admin.categorias.index')
                ->with('error', 'Não é possível excluir uma categoria com produtos vinculados.');
        }

        $categoria->delete();

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }

    public function updateOrder(Request $request)
    {
        $tenantId = session('tenant_id');

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:categories,id',
        ]);

        foreach ($request->order as $position => $id) {
            Category::where('tenant_id', $tenantId)->where('id', $id)->update(['order' => $position]);
        }

        return response()->json(['success' => true]);
    }
}
