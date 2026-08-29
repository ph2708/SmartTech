<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreSettingsController extends Controller
{
    public function edit()
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $tenant = $tenantId ? Tenant::find($tenantId) : Tenant::first();

        if (!$tenant) {
            $tenant = Tenant::firstOrCreate(
                ['slug' => 'smarttech'],
                ['name' => 'Smart Tech', 'whatsapp' => '64992495817']
            );
        }

        session(['tenant_id' => $tenant->id, 'tenant' => $tenant]);

        return view('admin.settings.edit', compact('tenant'));
    }

    public function update(Request $request)
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $tenant = $tenantId ? Tenant::find($tenantId) : Tenant::first();

        if (!$tenant) {
            return back()->with('error', 'Loja não encontrada.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'instagram' => 'nullable|string|max:255',
            'show_instagram' => 'nullable|boolean',
            'facebook' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // SMTP / E-mail
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl,none',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',

            // SMS
            'sms_enabled' => 'boolean',
            'sms_provider' => 'nullable|string|in:twilio,zenvia,totalvoice,log',
            'sms_api_key' => 'nullable|string|max:255',
            'sms_api_secret' => 'nullable|string|max:255',
            'sms_from_number' => 'nullable|string|max:50',

            // Modo do Sistema (SaaS vs Loja Única)
            'platform_mode' => 'nullable|in:single_store,saas',
            'allow_registration' => 'nullable|boolean',

            // Emissão Fiscal Focus NFe
            'nfe_enabled' => 'nullable|boolean',
            'nfe_environment' => 'nullable|in:homologacao,producao',
            'nfe_token' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'inscricao_estadual' => 'nullable|string|max:20',
            'inscricao_municipal' => 'nullable|string|max:20',
            'regime_tributario' => 'nullable|string|max:5',
        ]);

        $validated['sms_enabled'] = $request->has('sms_enabled');
        $validated['show_instagram'] = $request->has('show_instagram');
        $validated['nfe_enabled'] = $request->has('nfe_enabled');

        // Se o usuário tiver permissão de admin, atualiza as configurações globais do modo de plataforma
        if (auth()->user()?->isAdmin() || auth()->user()?->isSuperAdmin()) {
            if ($request->has('platform_mode')) {
                \App\Models\SystemSetting::set('single_store_mode', $request->input('platform_mode') === 'single_store' ? '1' : '0');
            }
            \App\Models\SystemSetting::set('allow_registration', $request->has('allow_registration') ? '1' : '0');
        }

        if ($request->hasFile('logo')) {
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $validated['logo'] = $request->file('logo')->store("tenants/{$tenant->id}", 'public');
        }

        $tenant->update($validated);
        session(['tenant' => $tenant->fresh()]);

        return redirect()->route('admin.configuracoes.edit')
            ->with('success', 'Configurações da loja salvas com sucesso!');
    }

    /**
     * Verificador / Testador de Conexão SMTP em Tempo Real
     */
    public function testSmtp(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $tenant = Tenant::find(session('tenant_id'));
        $testEmail = $request->input('test_email');

        // Se o tenant não configurou SMTP próprio, testa o SMTP padrão do sistema
        $host = $tenant->mail_host ?: config('mail.mailers.smtp.host');
        $port = $tenant->mail_port ?: config('mail.mailers.smtp.port');
        $username = $tenant->mail_username ?: config('mail.mailers.smtp.username');
        $password = $tenant->mail_password ?: config('mail.mailers.smtp.password');
        $encryption = $tenant->mail_encryption ?: config('mail.mailers.smtp.encryption');
        $fromAddress = $tenant->mail_from_address ?: config('mail.from.address');
        $fromName = $tenant->mail_from_name ?: ($tenant->name ?: config('mail.from.name'));

        try {
            // Configura dinamicamente o mailer com os dados da loja
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $host,
                'mail.mailers.smtp.port' => $port,
                'mail.mailers.smtp.username' => $username,
                'mail.mailers.smtp.password' => $password,
                'mail.mailers.smtp.encryption' => ($encryption === 'none' ? null : $encryption),
                'mail.from.address' => $fromAddress,
                'mail.from.name' => $fromName,
            ]);

            \Illuminate\Support\Facades\Mail::raw(
                "✅ Sucesso! Esta é uma mensagem de teste do verificador de SMTP da loja {$tenant->name}. Suas configurações de e-mail estão 100% validadas e prontas para disparos!",
                function ($message) use ($testEmail, $tenant) {
                    $message->to($testEmail)
                            ->subject("✅ Teste de Verificação SMTP - {$tenant->name}");
                }
            );

            // Marca como verificado com sucesso
            $tenant->update([
                'mail_is_verified' => true,
                'mail_verified_at' => now(),
            ]);
            session(['tenant' => $tenant->fresh()]);

            return response()->json([
                'success' => true,
                'message' => "E-mail de teste enviado com sucesso para {$testEmail}! O servidor SMTP foi validado com sucesso.",
            ]);
        } catch (\Exception $e) {
            $tenant->update(['mail_is_verified' => false]);
            session(['tenant' => $tenant->fresh()]);

            return response()->json([
                'success' => false,
                'message' => 'Falha na conexão SMTP: ' . $e->getMessage(),
            ], 422);
        }
    }
}
