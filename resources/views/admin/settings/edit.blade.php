@extends('layouts.admin')
@section('title', 'Configurações da Loja')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>⚙️ Configurações da Loja & Integrações</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.configuracoes.update') }}" enctype="multipart/form-data" class="admin-form">
            @csrf @method('PUT')

            <!-- Informações Básicas -->
            <div class="form-section-title">1. Informações Básicas</div>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="name">Nome da Loja *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $tenant->name) }}" required>
                </div>
                <div class="form-group flex-1">
                    <label for="whatsapp">WhatsApp Principal *</label>
                    <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $tenant->whatsapp) }}" required placeholder="64 99249-5817">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Descrição da Loja</label>
                <textarea id="description" name="description" rows="2">{{ old('description', $tenant->description) }}</textarea>
            </div>

            <!-- Endereço -->
            <div class="form-section-title">2. Endereço Físico</div>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="address">Endereço</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $tenant->address) }}" placeholder="Rua, número, bairro">
                </div>
                <div class="form-group">
                    <label for="city">Cidade</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $tenant->city) }}">
                </div>
                <div class="form-group" style="max-width:100px">
                    <label for="state">UF</label>
                    <input type="text" id="state" name="state" value="{{ old('state', $tenant->state) }}" maxlength="2" placeholder="GO">
                </div>
            </div>

            <!-- Redes Sociais -->
            <div class="form-section-title">3. Redes Sociais & Contato</div>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="instagram">Instagram (Nome de usuário ou Link)</label>
                    <input type="text" id="instagram" name="instagram" value="{{ old('instagram', $tenant->instagram) }}" placeholder="@smarttech_oficial ou https://instagram.com/smarttech_oficial">
                    <div style="margin-top: 6px;">
                        <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; color: #475569; cursor: pointer;">
                            <input type="checkbox" name="show_instagram" value="1" {{ old('show_instagram', $tenant->show_instagram ?? true) ? 'checked' : '' }}>
                            <span>Exibir botão de Instagram no cabeçalho e rodapé da loja pública</span>
                        </label>
                    </div>
                </div>
                <div class="form-group flex-2">
                    <label for="facebook">Facebook (Link Completo)</label>
                    <input type="text" id="facebook" name="facebook" value="{{ old('facebook', $tenant->facebook) }}" placeholder="https://facebook.com/smarttech">
                </div>
            </div>

            <!-- Identidade Visual -->
            <div class="form-section-title">4. Identidade Visual (Cores & Logo)</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="primary_color">Cor Principal</label>
                    <div class="color-picker-group">
                        <input type="color" id="primary_color" name="primary_color" value="{{ old('primary_color', $tenant->primary_color) }}">
                        <input type="text" value="{{ $tenant->primary_color }}" class="color-value" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="secondary_color">Cor Secundária</label>
                    <div class="color-picker-group">
                        <input type="color" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $tenant->secondary_color) }}">
                        <input type="text" value="{{ $tenant->secondary_color }}" class="color-value" readonly>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Logo Atual</label>
                @if($tenant->logo_url)
                    <div class="current-image">
                        <img src="{{ $tenant->logo_url }}" alt="{{ $tenant->name }}" style="max-height:80px;border-radius:8px;">
                    </div>
                @else
                    <p class="help-text">Nenhuma logo cadastrada</p>
                @endif
            </div>

            <div class="form-group">
                <label for="logo">Alterar Logotipo</label>
                <input type="file" id="logo" name="logo" accept="image/*" class="file-input">
            </div>

            <!-- E-mail & SMTP Transacional -->
            <div class="form-section-title" style="display: flex; justify-content: space-between; align-items: center;">
                <span>5. Configurações de E-mail (SMTP) & Notificações</span>
                @if($tenant->mail_is_verified)
                    <span class="status-badge" style="background: #dcfce7; color: #15803d; font-size: 0.8rem; font-weight: bold;">
                        ✅ SMTP Verificado & Ativo
                    </span>
                @else
                    <span class="status-badge" style="background: #fef3c7; color: #b45309; font-size: 0.8rem;">
                        ⚠️ SMTP Não Testado
                    </span>
                @endif
            </div>
            <p class="help-text" style="margin-top: -10px; margin-bottom: 12px;">Usado para avisar seus clientes automaticamente quando aparelhos da assistência técnica estiverem prontos.</p>

            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="mail_host">Servidor SMTP (Host)</label>
                    <input type="text" id="mail_host" name="mail_host" value="{{ old('mail_host', $tenant->mail_host) }}" placeholder="mail.seudominio.com.br ou smtp.gmail.com">
                </div>
                <div class="form-group flex-1">
                    <label for="mail_port">Porta</label>
                    <input type="number" id="mail_port" name="mail_port" value="{{ old('mail_port', $tenant->mail_port ?? 587) }}" placeholder="587 ou 465">
                </div>
                <div class="form-group flex-1">
                    <label for="mail_encryption">Criptografia</label>
                    <select id="mail_encryption" name="mail_encryption">
                        <option value="tls" {{ old('mail_encryption', $tenant->mail_encryption) === 'tls' ? 'selected' : '' }}>TLS (Porta 587)</option>
                        <option value="ssl" {{ old('mail_encryption', $tenant->mail_encryption) === 'ssl' ? 'selected' : '' }}>SSL (Porta 465)</option>
                        <option value="none" {{ old('mail_encryption', $tenant->mail_encryption) === 'none' ? 'selected' : '' }}>Nenhuma</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mail_username">Usuário SMTP (E-mail)</label>
                    <input type="text" id="mail_username" name="mail_username" value="{{ old('mail_username', $tenant->mail_username) }}" placeholder="contato@seudominio.com.br">
                </div>
                <div class="form-group">
                    <label for="mail_password">Senha SMTP</label>
                    <input type="password" id="mail_password" name="mail_password" value="{{ old('mail_password', $tenant->mail_password) }}" placeholder="••••••••">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mail_from_address">E-mail de Envio (From)</label>
                    <input type="email" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $tenant->mail_from_address) }}" placeholder="contato@seudominio.com.br">
                </div>
                <div class="form-group">
                    <label for="mail_from_name">Nome do Remetente</label>
                    <input type="text" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $tenant->mail_from_name ?? $tenant->name) }}" placeholder="Smart Tech - Atendimento">
                </div>
            </div>

            <!-- Caixa do Verificador de SMTP em Tempo Real -->
            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 18px; border-radius: 8px; margin: 12px 0 20px 0;">
                <h4 style="margin: 0 0 8px 0; font-size: 0.95rem; color: #1e293b;">🔍 Testador e Verificador de SMTP</h4>
                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 12px;">Envie um e-mail de teste para verificar se suas credenciais SMTP estão funcionando perfeitamente antes de disparar para clientes.</p>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <input type="email" id="test_email" placeholder="Digite seu e-mail pessoal para receber o teste" style="flex: 1; min-width: 240px; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    <button type="button" onclick="runSmtpTest()" id="btnTestSmtp" class="btn btn-outline" style="background: white; font-weight: bold; border-color: #3b82f6; color: #2563eb;">
                        ⚡ Testar e Validar SMTP
                    </button>
                </div>
                <div id="smtpTestResult" style="margin-top: 12px; display: none; padding: 12px; border-radius: 6px; font-size: 0.85rem;"></div>
            </div>

            <!-- SMS Gateway -->
            <div class="form-section-title">6. Integração para Disparo de SMS</div>
            <p class="help-text">Base pronta para disparar SMS para celulares de clientes quando a OS estiver pronta.</p>

            <div class="form-check" style="margin-bottom: 14px;">
                <label>
                    <input type="checkbox" name="sms_enabled" value="1" {{ old('sms_enabled', $tenant->sms_enabled) ? 'checked' : '' }} onchange="toggleSmsFields(this.checked)">
                    <span><strong>Ativar disparos automáticos via SMS</strong></span>
                </label>
            </div>

            <div id="smsFields" style="{{ $tenant->sms_enabled ? '' : 'opacity: 0.6;' }}">
                <div class="form-row">
                    <div class="form-group flex-1">
                        <label for="sms_provider">Provedor de SMS</label>
                        <select id="sms_provider" name="sms_provider">
                            <option value="twilio" {{ old('sms_provider', $tenant->sms_provider) === 'twilio' ? 'selected' : '' }}>Twilio (Global)</option>
                            <option value="zenvia" {{ old('sms_provider', $tenant->sms_provider) === 'zenvia' ? 'selected' : '' }}>Zenvia (Brasil)</option>
                            <option value="totalvoice" {{ old('sms_provider', $tenant->sms_provider) === 'totalvoice' ? 'selected' : '' }}>TotalVoice</option>
                            <option value="log" {{ old('sms_provider', $tenant->sms_provider) === 'log' ? 'selected' : '' }}>Modo Simulação / Log</option>
                        </select>
                    </div>
                    <div class="form-group flex-1">
                        <label for="sms_from_number">Remetente / Telefone de Envio</label>
                        <input type="text" id="sms_from_number" name="sms_from_number" value="{{ old('sms_from_number', $tenant->sms_from_number) }}" placeholder="Ex: +556499999999 ou SmartTech">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="sms_api_key">API Key / Account SID</label>
                        <input type="text" id="sms_api_key" name="sms_api_key" value="{{ old('sms_api_key', $tenant->sms_api_key) }}" placeholder="Chave da sua conta do provedor SMS">
                    </div>
                    <div class="form-group">
                        <label for="sms_api_secret">API Secret / Auth Token</label>
                        <input type="password" id="sms_api_secret" name="sms_api_secret" value="{{ old('sms_api_secret', $tenant->sms_api_secret) }}" placeholder="Token secreto do provedor">
                    </div>
                </div>
            </div>

            <!-- Emissão Fiscal Focus NFe -->
            <div class="form-section-title">7. Emissão Fiscal de Notas (Focus NFe)</div>
            <p class="help-text">Integração opcional para emissão automática de NFC-e (Cupom Fiscal) e NF-e via API Focus NFe.</p>

            <div class="form-check" style="margin-bottom: 14px;">
                <label>
                    <input type="checkbox" name="nfe_enabled" value="1" {{ old('nfe_enabled', $tenant->nfe_enabled) ? 'checked' : '' }}>
                    <span><strong>Ativar emissão fiscal via Focus NFe</strong></span>
                </label>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 18px; border-radius: 8px; margin-bottom: 20px;">
                <div class="form-row">
                    <div class="form-group flex-2">
                        <label for="nfe_token">Token da API Focus NFe</label>
                        <input type="text" id="nfe_token" name="nfe_token" value="{{ old('nfe_token', $tenant->nfe_token) }}" placeholder="Ex: a1b2c3d4e5f6g7h8i9j0...">
                    </div>
                    <div class="form-group flex-1">
                        <label for="nfe_environment">Ambiente SEFAZ</label>
                        <select id="nfe_environment" name="nfe_environment">
                            <option value="homologacao" {{ old('nfe_environment', $tenant->nfe_environment) === 'homologacao' ? 'selected' : '' }}>🧪 Homologação (Testes)</option>
                            <option value="producao" {{ old('nfe_environment', $tenant->nfe_environment) === 'producao' ? 'selected' : '' }}>🚀 Produção (Validade Jurídica)</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cnpj">CNPJ do Emitente</label>
                        <input type="text" id="cnpj" name="cnpj" value="{{ old('cnpj', $tenant->cnpj) }}" placeholder="00.000.000/0000-00">
                    </div>
                    <div class="form-group">
                        <label for="inscricao_estadual">Inscrição Estadual (IE)</label>
                        <input type="text" id="inscricao_estadual" name="inscricao_estadual" value="{{ old('inscricao_estadual', $tenant->inscricao_estadual) }}" placeholder="Isento ou Nº da IE">
                    </div>
                </div>
            </div>

            <!-- Modo de Operação da Plataforma -->
            @php
                $singleStoreSetting = \App\Models\SystemSetting::get('single_store_mode');
                $currentSingleStore = $singleStoreSetting !== null ? filter_var($singleStoreSetting, FILTER_VALIDATE_BOOLEAN) : config('app.single_store_mode', false);
                $allowRegSetting = \App\Models\SystemSetting::get('allow_registration');
                $currentAllowReg = $allowRegSetting !== null ? filter_var($allowRegSetting, FILTER_VALIDATE_BOOLEAN) : config('app.allow_registration', true);
            @endphp

            <div class="form-section-title">8. Modo de Operação do Sistema (SaaS vs Loja Única)</div>
            <p class="help-text">Escolha como seu catálogo se comporta para os visitantes.</p>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 18px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <label style="display: flex; gap: 12px; cursor: pointer;">
                        <input type="radio" name="platform_mode" value="single_store" {{ $currentSingleStore ? 'checked' : '' }} style="margin-top: 4px;">
                        <div>
                            <strong style="color: #0f172a;">🏪 Modo Loja Única Isolada (Recomendado para uso próprio / franquia única)</strong>
                            <p style="margin: 2px 0 0 0; font-size: 0.85rem; color: #64748b;">Acessar a raiz do site (<code>/</code>) abre diretamente o catálogo da sua loja, sem tela de cadastro público ou menções a outras lojas.</p>
                        </div>
                    </label>

                    <label style="display: flex; gap: 12px; cursor: pointer;">
                        <input type="radio" name="platform_mode" value="saas" {{ !$currentSingleStore ? 'checked' : '' }} style="margin-top: 4px;">
                        <div>
                            <strong style="color: #0f172a;">🌐 Modo Plataforma SaaS Multi-Lojas</strong>
                            <p style="margin: 2px 0 0 0; font-size: 0.85rem; color: #64748b;">A raiz do site (<code>/</code>) exibe a Landing Page de apresentação da plataforma com vitrine de parceiros e links para cadastro de novas lojas.</p>
                        </div>
                    </label>

                    <div style="margin-top: 8px; padding-top: 12px; border-top: 1px dashed #cbd5e1;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="allow_registration" value="1" {{ $currentAllowReg ? 'checked' : '' }}>
                            <span style="font-size: 0.9rem;">Permitir cadastro público de novas lojas na página <code>/registro</code></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px;">
                <button type="submit" class="btn btn-primary" style="padding: 14px 28px; font-size: 1rem;">Salvar Todas as Configurações 💾</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.querySelectorAll('input[type="color"]').forEach(picker => {
    picker.addEventListener('input', function() {
        this.nextElementSibling.value = this.value;
    });
});

function toggleSmsFields(enabled) {
    document.getElementById('smsFields').style.opacity = enabled ? '1' : '0.6';
}

function runSmtpTest() {
    const email = document.getElementById('test_email').value;
    const btn = document.getElementById('btnTestSmtp');
    const resultBox = document.getElementById('smtpTestResult');

    if (!email) {
        alert('Por favor, informe um e-mail para receber a mensagem de teste.');
        return;
    }

    btn.disabled = true;
    btn.textContent = '⏳ Testando conexão SMTP...';
    resultBox.style.display = 'none';

    fetch('{{ route('admin.configuracoes.testSmtp') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ test_email: email })
    })
    .then(async r => {
        const data = await r.json();
        btn.disabled = false;
        btn.textContent = '⚡ Testar e Validar SMTP';
        resultBox.style.display = 'block';

        if (r.ok && data.success) {
            resultBox.style.background = '#dcfce7';
            resultBox.style.color = '#15803d';
            resultBox.style.border = '1px solid #86efac';
            resultBox.innerHTML = `<strong>✅ Sucesso!</strong> ${data.message}`;
        } else {
            resultBox.style.background = '#fee2e2';
            resultBox.style.color = '#dc2626';
            resultBox.style.border = '1px solid #fca5a5';
            resultBox.innerHTML = `<strong>❌ Erro:</strong> ${data.message || 'Falha ao conectar no servidor SMTP.'}`;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = '⚡ Testar e Validar SMTP';
        resultBox.style.display = 'block';
        resultBox.style.background = '#fee2e2';
        resultBox.style.color = '#dc2626';
        resultBox.style.border = '1px solid #fca5a5';
        resultBox.innerHTML = `<strong>❌ Erro na requisição:</strong> ${err.message}`;
    });
}
</script>
@endsection
