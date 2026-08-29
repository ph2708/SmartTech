# Guia de Deploy na HostGator (Hospedagem Compartilhada - Plano P)

Este projeto foi desenvolvido com **Laravel 11** sem dependência de filas em segundo plano (`queue worker` contínuo / Redis / Horizon), sendo 100% compatível com a hospedagem compartilhada da HostGator (cPanel / Apache / PHP 8.2+).

---

## 1. Estrutura de Arquivos no Servidor

Na raiz da sua conta cPanel (fora do `public_html`), crie a pasta do Laravel:

```text
/home/SEU_USUARIO/
├── public_html/               <-- Apenas o conteúdo da pasta /public do Laravel
│   ├── css/
│   ├── js/
│   ├── images/
│   ├── storage/ -> symlink para ../laravel/storage/app/public
│   ├── .htaccess
│   ├── index.php             <-- Modificado (passo 3)
│   └── robots.txt
└── laravel/                  <-- O restante do projeto Laravel
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── routes/
    ├── resources/
    ├── storage/
    ├── vendor/
    ├── .env                  <-- Arquivo com credenciais de produção
    ├── artisan
    └── composer.json
```

---

## 2. Preparando os Arquivos Localmente

Antes de subir para a HostGator, gere os arquivos compilados e as dependências:

```bash
# 1. Instalar dependências otimizadas para produção
composer install --no-dev --optimize-autoloader

# 2. Compilar assets (CSS/JS) caso use Vite ou mantenha os arquivos de public/css
npm run build (se aplicável)
```

Compacte os arquivos em um `.zip`:
- **Opção A (Recomendada):** Suba o `.zip` pelo **Gerenciador de Arquivos do cPanel** e descompacte direto no servidor.
- **Opção B:** Envie via **FTP / FileZilla**.

---

## 3. Ajustando o `public_html/index.php`

Abra o arquivo `/public_html/index.php` no cPanel e ajuste os caminhos para apontar para a pasta `../laravel`:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Caminho para manutenção
if (file_exists($maintenance = __DIR__.'/../laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload do Composer
require __DIR__.'/../laravel/vendor/autoload.php';

// Inicialização do App Laravel
(require_once __DIR__.'/../laravel/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

---

## 4. Configuração do `.env` na Produção

No cPanel, dentro de `/home/SEU_USUARIO/laravel/.env`:

```env
APP_NAME="SmartTech Catálogo"
APP_ENV=production
APP_KEY=base64:sok04DayTi/jtLNQI3jlujAJFvtxL/Af/fVuzT7fMjo=
APP_DEBUG=false
APP_URL=https://seudominio.com.br

APP_TIMEZONE=America/Sao_Paulo
APP_LOCALE=pt_BR

# Banco de dados criado no cPanel MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seuusuario_smarttech
DB_USERNAME=seuusuario_smartuser
DB_PASSWORD=SuaSenhaForteAqui

# Sem filas em background (100% síncrono para o Plano P)
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
CACHE_STORE=database
FILESYSTEM_DISK=public

# Modo de Funcionamento:
# true  = LOJA ÚNICA ISOLADA (Abre direto o catálogo da SmartTech na raiz '/')
# false = PLATAFORMA SAAS (Mostra landing page e permite vender para outras lojas)
SINGLE_STORE_MODE=true
DEFAULT_STORE_SLUG=smarttech
ALLOW_REGISTRATION=false

MAIL_MAILER=smtp
MAIL_HOST=mail.seudominio.com.br
MAIL_PORT=465
MAIL_USERNAME=contato@seudominio.com.br
MAIL_PASSWORD=SuaSenhaEmail
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="contato@seudominio.com.br"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 5. Criação do Banco de Dados no cPanel

1. Acesse o **cPanel** > **Bancos de dados MySQL**.
2. Crie um novo banco (ex: `usuario_smarttech`).
3. Crie um novo usuário com senha forte (ex: `usuario_smartuser`).
4. Adicione o usuário ao banco e marque **"TODOS OS PRIVILÉGIOS"**.
5. No terminal SSH da HostGator (ou via phpMyAdmin importando o SQL inicial), execute:
   ```bash
   cd /home/SEU_USUARIO/laravel
   php artisan migrate --force --seed
   ```

---

## 6. Criação do Link Simbólico do Storage

No terminal SSH do cPanel:

```bash
cd /home/SEU_USUARIO/public_html
ln -s /home/SEU_USUARIO/laravel/storage/app/public storage
```

*Caso não tenha acesso SSH:*
Você pode criar uma rota temporária no `routes/web.php` para rodar o link:
```php
Route::get('/symlink', function () {
    Artisan::call('storage:link');
    return 'Storage linked com sucesso!';
});
```
*(Após acessar uma vez no navegador, remova essa rota por segurança).*

---

## 7. Permissões de Pastas

Verifique se as pastas abaixo têm permissão de escrita (**775** ou **755** dependendo do cPanel):
- `laravel/storage/` (e todas as subpastas)
- `laravel/bootstrap/cache/`

---

## 8. Agendador de Tarefas (Cron Job do cPanel) - Opcional

No cPanel > **Tarefas Cron (Cron Jobs)**, adicione para rodar a cada minuto (`* * * * *`):

```bash
/opt/cpanel/ea-php82/root/usr/bin/php /home/SEU_USUARIO/laravel/artisan schedule:run >> /dev/null 2>&1
```

---

## 9. Contas de Acesso Padrão (Criadas no Seed)

- **Super Admin:** `admin@smarttech.com` | Senha: `123456`
- **Loja SmartTech:** `smarttech@smarttech.com` | Senha: `123456`
- **URL da Loja:** `https://seudominio.com.br/loja/smarttech`
