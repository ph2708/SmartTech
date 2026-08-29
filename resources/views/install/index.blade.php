<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistente de Implantação HostGator | SmartCatálogo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 40px 20px; display: flex; justify-content: center; }
        .box { width: 100%; max-width: 680px; background: #1e293b; border-radius: 16px; padding: 32px; border: 1px solid #334155; box-shadow: 0 10px 40px rgba(0,0,0,0.4); }
        h1 { font-size: 1.6rem; margin-top: 0; display: flex; align-items: center; gap: 10px; }
        .badge { padding: 4px 10px; border-radius: 50px; font-size: 0.8rem; font-weight: bold; }
        .badge-ok { background: #065f46; color: #34d399; }
        .badge-err { background: #991b1b; color: #fca5a5; }
        .check-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #334155; }
        .actions { margin-top: 24px; display: flex; flex-direction: column; gap: 12px; }
        button { padding: 14px; font-weight: bold; border-radius: 8px; border: none; cursor: pointer; font-size: 0.95rem; font-family: inherit; transition: 0.2s; }
        .btn-primary { background: #e63946; color: white; }
        .btn-primary:hover { background: #d62839; }
        .btn-sec { background: #3b82f6; color: white; }
        .btn-sec:hover { background: #2563eb; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; white-space: pre-wrap; }
        .alert-success { background: #065f46; color: #ecfdf5; border: 1px solid #059669; }
        .alert-danger { background: #991b1b; color: #fef2f2; border: 1px solid #dc2626; }
    </style>
</head>
<body>
    <div class="box">
        <h1>⚡ Assistente de Implantação (HostGator)</h1>
        <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 24px;">Esta ferramenta executa a preparação do banco de dados e arquivos sem precisar de acesso terminal SSH.</p>

        @if(session('success'))
            <div class="alert alert-success">✅ <strong>Sucesso:</strong><br>{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">❌ <strong>Erro:</strong><br>{{ session('error') }}</div>
        @endif

        <h3 style="font-size: 1.1rem; margin-bottom: 12px;">1. Diagnóstico do Servidor</h3>
        <div class="check-item">
            <span>Versão do PHP (Requer >= 8.2)</span>
            <span class="badge {{ $phpOk ? 'badge-ok' : 'badge-err' }}">{{ $phpVersion }} {{ $phpOk ? '✅' : '❌' }}</span>
        </div>
        <div class="check-item">
            <span>Conexão com Banco MySQL</span>
            <span class="badge {{ $dbConnected ? 'badge-ok' : 'badge-err' }}">
                {{ $dbConnected ? 'Conectado com Sucesso ✅' : 'Falha de Conexão ❌' }}
            </span>
        </div>
        @if(!$dbConnected)
        <p style="color: #fca5a5; font-size: 0.8rem; margin: 6px 0;">Erro: {{ $dbError }}<br><small>Verifique as credenciais no arquivo .env</small></p>
        @endif
        <div class="check-item">
            <span>Permissão de Escrita (storage/)</span>
            <span class="badge {{ $storageWritable ? 'badge-ok' : 'badge-err' }}">{{ $storageWritable ? 'OK ✅' : 'Sem Permissão (775) ❌' }}</span>
        </div>
        <div class="check-item">
            <span>Permissão de Escrita (bootstrap/cache/)</span>
            <span class="badge {{ $cacheWritable ? 'badge-ok' : 'badge-err' }}">{{ $cacheWritable ? 'OK ✅' : 'Sem Permissão (775) ❌' }}</span>
        </div>

        <h3 style="font-size: 1.1rem; margin-top: 24px; margin-bottom: 12px;">2. Executar Configurações Iniciais</h3>
        <div class="actions">
            <form method="POST" action="{{ route('install.run') }}">
                @csrf
                <input type="hidden" name="action" value="migrate_seed">
                <button type="submit" class="btn-primary" style="width: 100%;">
                    🚀 1. Criar Tabelas e Dados Iniciais (Migrate & Seed)
                </button>
            </form>

            <form method="POST" action="{{ route('install.run') }}">
                @csrf
                <input type="hidden" name="action" value="storage_link">
                <button type="submit" class="btn-sec" style="width: 100%;">
                    🔗 2. Conectar Pasta de Imagens (Storage Link)
                </button>
            </form>

            <form method="POST" action="{{ route('install.run') }}">
                @csrf
                <input type="hidden" name="action" value="clear_cache">
                <button type="submit" style="background: #475569; color: white; width: 100%;">
                    🧹 3. Limpar Cache do Sistema
                </button>
            </form>
        </div>

        <div style="margin-top: 24px; text-align: center; border-top: 1px solid #334155; padding-top: 16px;">
            <a href="{{ url('/') }}" style="color: #38bdf8; text-decoration: none; font-weight: bold; font-size: 0.9rem;">
                Ir para o Catálogo da Loja →
            </a>
        </div>
    </div>
</body>
</html>
