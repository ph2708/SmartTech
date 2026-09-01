import os
import ftplib
import zipfile
import tempfile
import sys

HOST = '50.116.112.21'
USER = 'admin@arthurrodriguessanto1787945897778.0210362.meusitehostgator.com.br'
PASS = 'MAQ@2708'
BASE_DIR = '/Users/phelipesc/Documents/projetos/projeto_smarttech'

print("==> 1. Conectando ao FTP...")
ftp = ftplib.FTP(HOST, USER, PASS, timeout=30)
ftp.set_pasv(True)

def ensure_ftp_dir(remote_dir):
    dirs = [d for d in remote_dir.split('/') if d]
    current = ""
    for d in dirs:
        current += "/" + d
        try:
            ftp.cwd(current)
        except:
            ftp.mkd(current)
            ftp.cwd(current)
    ftp.cwd("/")

def upload_file(local_path, remote_path):
    remote_dir = os.path.dirname(remote_path)
    if remote_dir and remote_dir != '/':
        ensure_ftp_dir(remote_dir)
    filename = os.path.basename(remote_path)
    if remote_dir:
        ftp.cwd(remote_dir)
    else:
        ftp.cwd('/')
    with open(local_path, 'rb') as f:
        ftp.storbinary(f"STOR {filename}", f)
    ftp.cwd('/')

def upload_dir_recursive(local_dir, remote_base, ignore_patterns=[]):
    for root, dirs, files in os.walk(local_dir):
        # Filter ignore dirs
        dirs[:] = [d for d in dirs if not any(p in os.path.join(root, d) for p in ignore_patterns)]
        rel_path = os.path.relpath(root, local_dir)
        if rel_path == '.':
            remote_target = remote_base
        else:
            remote_target = os.path.join(remote_base, rel_path).replace('\\', '/')
        
        for file in files:
            if any(p in file for p in ignore_patterns):
                continue
            local_file = os.path.join(root, file)
            remote_file = os.path.join(remote_target, file).replace('\\', '/')
            try:
                upload_file(local_file, remote_file)
                print(f"Uploaded: {remote_file}")
            except Exception as e:
                print(f"Error uploading {local_file} -> {remote_file}: {e}")

# Prepare index.php for cPanel
temp_index = os.path.join(tempfile.gettempdir(), 'cpanel_index.php')
with open(os.path.join(BASE_DIR, 'public/index.php'), 'r') as f:
    content = f.read()

# Replace paths to point to ../laravel
content = content.replace("__DIR__.'/../vendor/autoload.php'", "__DIR__.'/../laravel/vendor/autoload.php'")
content = content.replace("__DIR__.'/../bootstrap/app.php'", "__DIR__.'/../laravel/bootstrap/app.php'")
with open(temp_index, 'w') as f:
    f.write(content)

print("==> 2. Enviando pasta /public_html...")
upload_dir_recursive(
    os.path.join(BASE_DIR, 'public'),
    '/public_html',
    ignore_patterns=['index.php', 'storage', '.git']
)

# Upload adjusted index.php to /public_html/index.php
upload_file(temp_index, '/public_html/index.php')
print("Uploaded: /public_html/index.php (ajustado para ../laravel)")

# Upload .htaccess to /public_html/.htaccess
upload_file(os.path.join(BASE_DIR, 'public/.htaccess'), '/public_html/.htaccess')
print("Uploaded: /public_html/.htaccess")

print("==> 3. Enviando estrutura do sistema para /laravel...")
# Directories to upload to /laravel
for folder in ['app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'vendor']:
    print(f"--- Enviando pasta {folder}/ para /laravel/{folder} ---")
    upload_dir_recursive(
        os.path.join(BASE_DIR, folder),
        f'/laravel/{folder}',
        ignore_patterns=['.git', 'cache/*.php']
    )

# Root files for /laravel
for f in ['artisan', 'composer.json', 'composer.lock']:
    if os.path.exists(os.path.join(BASE_DIR, f)):
        upload_file(os.path.join(BASE_DIR, f), f'/laravel/{f}')
        print(f"Uploaded: /laravel/{f}")

# Create storage directory structure in /laravel
storage_dirs = [
    '/laravel/storage',
    '/laravel/storage/app',
    '/laravel/storage/app/public',
    '/laravel/storage/framework',
    '/laravel/storage/framework/cache',
    '/laravel/storage/framework/cache/data',
    '/laravel/storage/framework/sessions',
    '/laravel/storage/framework/views',
    '/laravel/storage/logs',
    '/laravel/bootstrap/cache'
]
for sdir in storage_dirs:
    ensure_ftp_dir(sdir)
    print(f"Created/Verified storage dir: {sdir}")

# Create .env.example / .env template
env_prod = """APP_NAME=SmartCatálogo
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

temp_env = os.path.join(tempfile.gettempdir(), 'prod_env')
with open(temp_env, 'w') as f:
    f.write(env_prod)

upload_file(temp_env, '/laravel/.env.example')
print("Uploaded: /laravel/.env.example")

ftp.quit()
print("==> DEPLOY FTP CONCLUÍDO COM SUCESSO! <==")
