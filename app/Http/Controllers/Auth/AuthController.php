<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Muitas tentativas de login incorretas. Tente novamente em {$seconds} segundos.",
            ])->onlyInput('email');
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Seu acesso de usuário foi desativado pelo administrador da loja.',
                ]);
            }

            if ($user->isSuperAdmin()) {
                return redirect()->route('superadmin.tenants.index');
            }

            // Set tenant in session
            if ($user->tenant_id) {
                $tenant = Tenant::find($user->tenant_id);
                if (!$tenant || !$tenant->is_active) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Sua loja está desativada. Entre em contato com o suporte.',
                    ]);
                }
                session(['tenant_id' => $tenant->id]);
                session(['tenant' => $tenant]);
            }

            return redirect()->route('admin.dashboard');
        }

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        $allowRegSetting = \App\Models\SystemSetting::get('allow_registration');
        $allowReg = $allowRegSetting !== null ? filter_var($allowRegSetting, FILTER_VALIDATE_BOOLEAN) : config('app.allow_registration', true);

        if (!$allowReg) {
            return redirect()->route('login')->withErrors(['email' => 'Novos cadastros de lojas estão temporariamente desativados pelo administrador.']);
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $allowRegSetting = \App\Models\SystemSetting::get('allow_registration');
        $allowReg = $allowRegSetting !== null ? filter_var($allowRegSetting, FILTER_VALIDATE_BOOLEAN) : config('app.allow_registration', true);

        if (!$allowReg) {
            abort(403, 'Cadastro desativado.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'store_name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $validated['store_name'],
            'slug' => Str::slug($validated['store_name']) . '-' . Str::random(4),
            'whatsapp' => $validated['whatsapp'],
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tenant_id' => $tenant->id,
            'role' => 'admin',
        ]);

        // Create default categories
        $defaultCategories = [
            ['name' => 'Assistência Técnica - Celulares', 'slug' => 'assistencia-celulares', 'icon' => '📱', 'order' => 1],
            ['name' => 'Assistência Técnica - Computadores', 'slug' => 'assistencia-computadores', 'icon' => '💻', 'order' => 2],
            ['name' => 'Capinhas e Películas', 'slug' => 'capinhas-peliculas', 'icon' => '🛡️', 'order' => 3],
            ['name' => 'Acessórios', 'slug' => 'acessorios', 'icon' => '🎧', 'order' => 4],
            ['name' => 'Perfumes', 'slug' => 'perfumes', 'icon' => '🧴', 'order' => 5],
        ];

        foreach ($defaultCategories as $cat) {
            $tenant->categories()->create($cat + ['tenant_id' => $tenant->id]);
        }

        // Notifica o Super Admin / Dono da Plataforma sobre o novo cadastro
        try {
            $superAdmin = User::where('role', 'super_admin')->first();
            if ($superAdmin && $superAdmin->email) {
                \Illuminate\Support\Facades\Mail::raw(
                    "🎉 Nova loja parceira cadastrada na plataforma!\n\n" .
                    "Loja: {$tenant->name}\n" .
                    "Responsável: {$user->name}\n" .
                    "WhatsApp: {$tenant->whatsapp}\n" .
                    "E-mail: {$user->email}\n" .
                    "Catálogo: " . route('catalog.store', $tenant->slug) . "\n\n" .
                    "Acesse o painel Super Admin para gerenciar: " . route('superadmin.tenants.index'),
                    function ($msg) use ($superAdmin, $tenant) {
                        $msg->to($superAdmin->email)
                            ->subject("🚀 Nova Loja Cadastrada: {$tenant->name} - SmartCatálogo SaaS");
                    }
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::info("Novo cadastro SaaS: {$tenant->name} ({$user->email}) - WhatsApp: {$tenant->whatsapp}");
        }

        Auth::login($user);
        session(['tenant_id' => $tenant->id]);
        session(['tenant' => $tenant]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Bem-vindo! Sua loja foi criada com sucesso. Comece adicionando seus produtos!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
