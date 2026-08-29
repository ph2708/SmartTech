<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BranchController extends Controller
{
    /**
     * Lista de filiais da loja matriz
     */
    public function index()
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $tenant = Tenant::find($tenantId);

        // Se o usuário já estiver em uma filial, redireciona para a matriz ou exibe suas filiais irmãs
        $mainTenant = $tenant->is_branch && $tenant->parent_id ? $tenant->parent : $tenant;
        $branches = $mainTenant->branches()->withCount(['products', 'users'])->get();

        return view('admin.branches.index', compact('mainTenant', 'branches', 'tenant'));
    }

    /**
     * Formulário de nova filial
     */
    public function create()
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $mainTenant = Tenant::find($tenantId);

        return view('admin.branches.create', compact('mainTenant'));
    }

    /**
     * Salva nova filial
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $mainTenant = Tenant::find($tenantId);

        $validated = $request->validate([
            'branch_name' => 'required|string|max:100',
            'whatsapp' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'copy_products' => 'nullable|boolean',
        ]);

        $slug = Str::slug($mainTenant->name . '-' . $validated['branch_name']) . '-' . Str::random(3);

        $branch = Tenant::create([
            'parent_id' => $mainTenant->id,
            'is_branch' => true,
            'branch_name' => $validated['branch_name'],
            'name' => $mainTenant->name . ' (' . $validated['branch_name'] . ')',
            'slug' => $slug,
            'whatsapp' => $validated['whatsapp'],
            'address' => $validated['address'],
            'city' => $validated['city'] ?? $mainTenant->city,
            'state' => $validated['state'] ?? $mainTenant->state,
            'primary_color' => $mainTenant->primary_color,
            'secondary_color' => $mainTenant->secondary_color,
            'logo' => $mainTenant->logo,
            'is_active' => true,
            'plan' => $mainTenant->plan,
        ]);

        // Copia as categorias da matriz para a filial
        foreach ($mainTenant->categories as $cat) {
            $newCat = $branch->categories()->create([
                'tenant_id' => $branch->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'icon' => $cat->icon,
                'description' => $cat->description,
                'order' => $cat->order,
                'is_active' => $cat->is_active,
            ]);

            // Se solicitado, clona produtos da matriz
            if ($request->has('copy_products')) {
                foreach ($cat->products as $prod) {
                    $branch->products()->create([
                        'tenant_id' => $branch->id,
                        'category_id' => $newCat->id,
                        'name' => $prod->name,
                        'slug' => $prod->slug . '-' . Str::random(3),
                        'price' => $prod->price,
                        'promotional_price' => $prod->promotional_price,
                        'type' => $prod->type,
                        'manage_stock' => $prod->manage_stock,
                        'stock_quantity' => 0, // Inicia com 0 para a filial lançar o seu estoque local
                        'min_stock_alert' => $prod->min_stock_alert,
                        'description' => $prod->description,
                        'specifications' => $prod->specifications,
                        'image' => $prod->image,
                        'is_active' => $prod->is_active,
                    ]);
                }
            }
        }

        return redirect()->route('admin.filiais.index')
            ->with('success', "Filial '{$branch->name}' criada com sucesso!");
    }

    /**
     * Alterna o contexto da loja ativa na sessão (Troca rápida entre Matriz e Filiais)
     */
    public function switchBranch(Tenant $branch)
    {
        $user = auth()->user();

        // Se for admin ou super_admin, permite alternar
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            session(['tenant_id' => $branch->id]);
            session(['tenant' => $branch]);

            return redirect()->route('admin.dashboard')
                ->with('success', "Você agora está gerenciando a unidade: {$branch->name}");
        }

        return back()->with('error', 'Sem permissão para alternar filiais.');
    }
}
