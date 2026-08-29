<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $currentTenant = \App\Models\Tenant::find($tenantId);
        $mainTenant = $currentTenant->is_branch && $currentTenant->parent_id ? $currentTenant->parent : $currentTenant;
        
        // Se for admin da matriz, pode ver e gerenciar usuários de toda a rede
        $accessibleTenantIds = $mainTenant->branches()->pluck('id')->push($mainTenant->id);
        
        $users = User::whereIn('tenant_id', $accessibleTenantIds)->with('tenant')->latest()->paginate(15);
        $totalUsers = User::whereIn('tenant_id', $accessibleTenantIds)->count();
        $activeUsers = User::whereIn('tenant_id', $accessibleTenantIds)->where('is_active', true)->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'activeUsers', 'mainTenant'));
    }

    public function create()
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $currentTenant = \App\Models\Tenant::find($tenantId);
        $mainTenant = $currentTenant->is_branch && $currentTenant->parent_id ? $currentTenant->parent : $currentTenant;
        
        $availableUnits = $mainTenant->branches()->where('is_active', true)->get()->prepend($mainTenant);

        return view('admin.users.create', compact('availableUnits', 'currentTenant'));
    }

    public function store(Request $request)
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $currentTenant = \App\Models\Tenant::find($tenantId);
        $mainTenant = $currentTenant->is_branch && $currentTenant->parent_id ? $currentTenant->parent : $currentTenant;
        $allowedTenantIds = $mainTenant->branches()->pluck('id')->push($mainTenant->id)->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,tecnico,atendente',
            'tenant_id' => 'required|in:' . implode(',', $allowedTenantIds),
            'password' => 'required|string|min:6|confirmed',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário vinculado e cadastrado com sucesso!');
    }

    public function edit(User $usuario)
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $currentTenant = \App\Models\Tenant::find($tenantId);
        $mainTenant = $currentTenant->is_branch && $currentTenant->parent_id ? $currentTenant->parent : $currentTenant;
        $allowedTenantIds = $mainTenant->branches()->pluck('id')->push($mainTenant->id)->toArray();

        if (!in_array($usuario->tenant_id, $allowedTenantIds) && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }

        $availableUnits = $mainTenant->branches()->where('is_active', true)->get()->prepend($mainTenant);

        return view('admin.users.edit', ['user' => $usuario, 'availableUnits' => $availableUnits]);
    }

    public function update(Request $request, User $usuario)
    {
        $tenantId = session('tenant_id');
        if ($usuario->tenant_id !== $tenantId) {
            abort(403, 'Acesso não autorizado.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($usuario->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,tecnico,atendente',
            'password' => 'nullable|string|min:6|confirmed',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $usuario->update($validated);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function toggleActive(User $user)
    {
        $tenantId = session('tenant_id');
        if ($user->tenant_id !== $tenantId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Não permitir desativar o próprio usuário logado
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Não é possível desativar sua própria conta.'], 422);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active,
        ]);
    }

    public function destroy(User $usuario)
    {
        $tenantId = session('tenant_id');
        if ($usuario->tenant_id !== $tenantId) {
            abort(403, 'Acesso não autorizado.');
        }

        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'Você não pode excluir sua própria conta de usuário.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}
