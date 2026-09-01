import os
import sys
import zipfile
import ftplib
import tempfile
import time

HOST = '50.116.112.21'
USER = 'admin@arthurrodriguessanto1787945897778.0210362.meusitehostgator.com.br'
PASS = 'MAQ@2708'
BASE_DIR = '/Users/phelipesc/Documents/projetos/projeto_smarttech'

print("==> 1. Compactando projeto em arquivo ZIP...")
zip_filename = os.path.join(tempfile.gettempdir(), 'smarttech_deploy.zip')

with zipfile.ZipFile(zip_filename, 'w', zipfile.ZIP_DEFLATED) as zipf:
    # Folders for /laravel
    for folder in ['app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'vendor']:
        folder_path = os.path.join(BASE_DIR, folder)
        for root, dirs, files in os.walk(folder_path):
            dirs[:] = [d for d in dirs if d != '.git' and d != 'node_modules']
            for file in files:
                if file.endswith('.git') or 'cache/data' in root:
                    continue
                file_path = os.path.join(root, file)
                arcname = 'laravel/' + os.path.relpath(file_path, BASE_DIR)
                zipf.write(file_path, arcname)

    # Root files for /laravel
    for f in ['artisan', 'composer.json', 'composer.lock']:
        fp = os.path.join(BASE_DIR, f)
        if os.path.exists(fp):
            zipf.write(fp, f'laravel/{f}')

    # Public files for /public_html
    public_path = os.path.join(BASE_DIR, 'public')
    for root, dirs, files in os.walk(public_path, followlinks=False):
        dirs[:] = [d for d in dirs if d != '.git' and d != 'storage']
        for file in files:
            if file in ['index.php', 'storage']:
                continue
            file_path = os.path.join(root, file)
            if not os.path.exists(file_path) or os.path.islink(file_path):
                continue
            arcname = 'public_html/' + os.path.relpath(file_path, public_path)
            zipf.write(file_path, arcname)

    # Add modified index.php for cPanel
    index_cpanel = """<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../laravel/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../laravel/bootstrap/app.php')
    ->handleRequest(Request::capture());
"""
    zipf.writestr('public_html/index.php', index_cpanel)

    # Add .env template for production
    env_content = """APP_NAME=SmartCatálogo
APP_ENV=production
APP_KEY=base64:7V9mBqmD9jVqE3xT2u7W/qN3Kk5L8p9S1a2b3c4d5e6=
APP_DEBUG=true
APP_TIMEZONE=America/Sao_Paulo
APP_URL=http://arthurrodriguessanto1787945897778.0210362.meusitehostgator.com.br

SINGLE_STORE_MODE=false
ALLOW_REGISTRATION=true

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arth8113_smarttech
DB_USERNAME=arth8113_admin
DB_PASSWORD=SuaSenhaDoBanco

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync

CACHE_STORE=file
"""
    zipf.writestr('laravel/.env', env_content)

print(f"ZIP criado com sucesso ({os.path.getsize(zip_filename) / (1024*1024):.2f} MB): {zip_filename}")

# Create PHP Extractor script
unzip_php = """<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== INICIANDO EXTRAÇÃO DO SMARTCATÁLOGO ===\\n\\n";

$zipFile = __DIR__ . '/smarttech_deploy.zip';

if (!file_exists($zipFile)) {
    die("ERRO: Arquivo $zipFile não encontrado!\\n");
}

$zip = new ZipArchive;
$res = $zip->open($zipFile);

if ($res === TRUE) {
    // Extrai no diretório pai (raiz da conta /)
    $dest = dirname(__DIR__);
    echo "Extraindo arquivos para: $dest ...\\n";
    $zip->extractTo($dest);
    $zip->close();
    echo "Extração concluída com sucesso!\\n\\n";

    // Cria diretórios de storage com permissões
    $dirs = [
        $dest . '/laravel/storage',
        $dest . '/laravel/storage/app',
        $dest . '/laravel/storage/app/public',
        $dest . '/laravel/storage/framework',
        $dest . '/laravel/storage/framework/cache',
        $dest . '/laravel/storage/framework/cache/data',
        $dest . '/laravel/storage/framework/sessions',
        $dest . '/laravel/storage/framework/views',
        $dest . '/laravel/storage/logs',
        $dest . '/laravel/bootstrap/cache',
    ];

    foreach ($dirs as $d) {
        if (!is_dir($d)) {
            mkdir($d, 0775, true);
        }
        @chmod($d, 0775);
    }
    echo "Estrutura de pastas /storage criada e permissões ajustadas!\\n\\n";

    // Remove o ZIP para economizar espaço
    @unlink($zipFile);
    echo "Arquivo ZIP temporário removido.\\n\\n";
    echo "=== PROCESSO FINALIZADO COM SUCESSO! ===\\n";
    echo "Acesse agora o instalador web em: /install para configurar o banco de dados!\\n";
} else {
    echo "ERRO ao abrir arquivo ZIP. Código de erro: $res\\n";
}
"""

unzip_file = os.path.join(tempfile.gettempdir(), 'unzip_deploy.php')
with open(unzip_file, 'w') as f:
    f.write(unzip_php)

print("==> 2. Conectando ao FTP para envio ultra-rápido...")
ftp = ftplib.FTP(HOST, USER, PASS, timeout=60)
ftp.set_pasv(True)

print("Enviando unzip_deploy.php para /public_html/unzip_deploy.php ...")
with open(unzip_file, 'rb') as f:
    ftp.storbinary('STOR /public_html/unzip_deploy.php', f)

print(f"Enviando smarttech_deploy.zip ({os.path.getsize(zip_filename) / (1024*1024):.2f} MB)...")
total_size = os.path.getsize(zip_filename)
uploaded = 0

def progress_callback(data):
    global uploaded
    uploaded += len(data)
    percent = (uploaded / total_size) * 100
    sys.stdout.write(f"\rProgresso do Upload: {percent:.1f}% ({uploaded/(1024*1024):.1f}MB / {total_size/(1024*1024):.1f}MB)")
    sys.stdout.flush()

with open(zip_filename, 'rb') as f:
    ftp.storbinary('STOR /public_html/smarttech_deploy.zip', f, callback=progress_callback)

print("\n\nUpload do pacote concluído via FTP!")
ftp.quit()

print("==> 3. Disparando script de extração automática no servidor web...")
import urllib.request

url = 'http://arthurrodriguessanto1787945897778.0210362.meusitehostgator.com.br/unzip_deploy.php'
print(f"Chamando: {url} ...")
try:
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    with urllib.request.urlopen(req, timeout=60) as response:
        html = response.read().decode('utf-8')
        print("\n--- RESPOSTA DO SERVIDOR HOSTGATOR: ---")
        print(html)
except Exception as e:
    print(f"Erro ao chamar script web: {e}")
