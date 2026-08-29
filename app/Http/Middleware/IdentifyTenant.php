<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Se o usuário já escolheu uma filial na sessão, mantém a filial escolhida. Caso contrário, usa o tenant_id do user
            $activeTenantId = session('tenant_id') ?? $user->tenant_id;

            if ($activeTenantId) {
                $tenant = Tenant::find($activeTenantId);

                if (!$tenant || !$tenant->is_active) {
                    auth()->logout();
                    return redirect('/login')->with('error', 'Sua loja está desativada. Entre em contato com o suporte.');
                }

                session(['tenant_id' => $tenant->id]);
                session(['tenant' => $tenant]);
            }
        }

        return $next($request);
    }
}
