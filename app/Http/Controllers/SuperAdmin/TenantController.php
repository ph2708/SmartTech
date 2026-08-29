<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount(['users', 'products', 'categories'])->latest()->get();
        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('superadmin.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug',
            'whatsapp' => 'required|string|max:20',
            'plan' => 'required|in:free,basic,pro',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6',
        ]);

        $tenant = Tenant::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'whatsapp' => $validated['whatsapp'],
            'plan' => $validated['plan'],
        ]);

        User::create([
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => bcrypt($validated['admin_password']),
            'tenant_id' => $tenant->id,
            'role' => 'admin',
        ]);

        return redirect()->route('superadmin.tenants.index')
            ->with('success', 'Loja criada com sucesso!');
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load('users');
        return view('superadmin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'plan' => 'required|in:free,basic,pro',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $tenant->update($validated);

        return redirect()->route('superadmin.tenants.index')
            ->with('success', 'Loja atualizada com sucesso!');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('superadmin.tenants.index')
            ->with('success', 'Loja excluída com sucesso!');
    }

    public function toggleActive(Tenant $tenant)
    {
        $tenant->update(['is_active' => !$tenant->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $tenant->is_active,
        ]);
    }
}
