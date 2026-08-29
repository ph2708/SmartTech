# ⚡ SmartCatálogo - Catálogo Online, Assistência Técnica & Gestão com WhatsApp

Plataforma completa em **Laravel 11**, desenvolvida sob medida para lojas e assistências técnicas de **celulares, computadores, notebooks, capinhas, acessórios e perfumes**.

O sistema une um **catálogo online responsivo com pedidos via WhatsApp** a um **painel administrativo robusto** contendo **controle de estoque**, **gestão de ordens de serviço (OS)** com laudo e impressão, **analytics de visitantes** e **controle financeiro de vendas**. 

Pode operar como **Loja Única Isolada** (site próprio da loja) ou como **Plataforma SaaS Multi-tenant** (para revenda a outras lojas), totalmente compatível com **Hospedagem Compartilhada (HostGator Plano P)** sem uso de filas em segundo plano (*queue workers*).

---

## 📑 Manual Operacional da Plataforma (Navegação Rápida)

Abaixo está o índice hiperlinkado de todos os estágios operacionais da plataforma. Clique em qualquer estágio para ir direto para as instruções detalhadas:

1. [🚀 **Estágio 1:** Instalação, Deploy & Setup Inicial](#-estagio-1-instalacao-deploy--setup-inicial)
2. [🎨 **Estágio 2:** Identidade Visual, Cores & Redes Sociais](#-estagio-2-identidade-visual-cores--redes-sociais)
3. [⚙️ **Estágio 3:** E-mail (SMTP), SMS & Emissão Fiscal (Focus NFe)](#-estagio-3-e-mail-smtp-sms--emissao-fiscal-focus-nfe)
4. [📦 **Estágio 4:** Gestão de Categorias, Catálogo Online, Estoque & Imobilizados](#-estagio-4-gestao-de-categorias-catalogo-online-estoque--imobilizados)
5. [🛠️ **Estágio 5:** Assistência Técnica & Gestão de Ordens de Serviço (OS)](#-estagio-5-assistencia-tecnica--gestao-de-ordens-de-servico-os)
6. [🛒 **Estágio 6:** Vendas de Balcão & Emissão de Cupom Fiscal (NFC-e)](#-estagio-6-vendas-de-balcao--emissao-de-cupom-fiscal-nfc-e)
7. [💵 **Estágio 7:** Controle Financeiro, Despesas & Fechamento de Caixa](#-estagio-7-controle-financeiro-despesas--fechamento-de-caixa)
8. [🏢 **Estágio 8:** Gestão de Filiais, Equipe & Troca de Unidade](#-estagio-8-gestao-de-filiais-equipe--troca-de-unidade)
9. [👑 **Estágio 9:** Administração SaaS & Controle de Lojistas (Super Admin)](#-estagio-9-administracao-saas--controle-de-lojistas-super-admin)

---

### 🚀 Estágio 1: Instalação, Deploy & Setup Inicial
- **Local com Docker:**
  - Execute `docker compose up -d --build` e acesse [http://localhost:8000](http://localhost:8000).
- **Produção na HostGator (cPanel / Plano P):**
  - Acesse o assistente web integrado em `https://seudominio.com.br/install`.
  - O instalador valida as permissões, cria as tabelas do banco com 1 clique (`migrate --force --seed`), conecta a pasta pública de imagens e cria a trava de segurança (`installed.lock`).

---

### 🎨 Estágio 2: Identidade Visual, Cores & Redes Sociais
No menu **`⚙️ Configurações`**:
- **Logotipo & Cores:** Envie a logo da loja e escolha as cores primária e secundária (com color-picker em tempo real).
- **WhatsApp:** Defina o número que receberá os pedidos dos clientes.
- **Instagram:** Insira o usuário (@sua_loja) ou link completo e marque `[x] Exibir botão de Instagram no cabeçalho e rodapé da loja pública`. Caso queira ocultar temporariamente, basta desmarcar o checkbox.

---

### ⚙️ Estágio 3: E-mail (SMTP), SMS & Emissão Fiscal (Focus NFe)
No menu **`⚙️ Configurações`**:
- **E-mail (SMTP Transacional):** Preencha seu host (ex: `mail.seudominio.com.br`), porta (587/465), usuário e senha.
  - **Testador em Tempo Real:** Utilize o botão **`⚡ Testar e Validar SMTP`** para receber um e-mail de teste e verificar a conexão.
- **SMS Gateway:** Habilite o envio de SMS configurando sua chave do provedor (Twilio, Zenvia ou TotalVoice).
- **Emissão Fiscal (Focus NFe):**
  - Marque `[x] Ativar emissão fiscal via Focus NFe`.
  - Insira o Token da API Focus NFe e selecione o ambiente (`🧪 Homologação` para testes ou `🚀 Produção` para validade fiscal).
  - Preencha CNPJ e Inscrição Estadual (IE).

---

### 📦 Estágio 4: Gestão de Categorias, Catálogo Online, Estoque & Imobilizados
No menu **`📦 Produtos / Estoque`**:
- **3 Tipos de Itens:**
  1. `📦 Produto Físico (Mercadoria)`: Para itens que possuem estoque físico e são vendidos a clientes.
  2. `🔧 Serviço (Assistência Técnica)`: Para mão de obra de consertos (estoque infinito).
  3. `🏛️ Bem Imobilizado / Patrimônio da Loja`: Para controle de móveis, cadeiras, bebedouros e ferramentas da empresa (não é exibido no catálogo online e não é vendido a clientes).
- **Canais de Exibição Independentes:**
  - `[x] 🌐 Exibir na Vitrine do Catálogo Online`: Define se o cliente verá o item no site.
  - `[x] 🛒 Disponível para Venda Física / Balcão`: Permite ao vendedor lançar o item em vendas de balcão e emitir nota fiscal, mesmo que esteja invisível no catálogo virtual.

---

### 🛠️ Estágio 5: Assistência Técnica & Gestão de Ordens de Serviço (OS)
No menu **`🛠️ Assistência / OS`**:
- **Entrada do Aparelho:** Cadastro do cliente, tipo de aparelho, marca, modelo, IMEI, senha de desbloqueio e acessórios deixados.
- **Comprovante de Entrada com Termo de 90 Dias:** Botão **`🖨️ Imprimir`** gera via pronta para assinatura do cliente.
- **Aviso Automático ao Cliente:** Ao alterar o status para **`Pronto p/ Retirada`**, o sistema dispara automaticamente um **e-mail profissional** para o cliente e gera o link direto para avisar no **WhatsApp**.
- **Emissão de NFS-e:** Botão **`🧾 NFS-e`** transmite os dados do conserto para a Prefeitura via Focus NFe.

---

### 🛒 Estágio 6: Vendas de Balcão & Emissão de Cupom Fiscal (NFC-e)
No menu **`🛒 Vendas / Pedidos`**:
- **Nova Venda:** Registra vendas presenciais de balcão, dando baixa imediata no estoque e lançando a receita no fluxo financeiro.
- **Emissão de Cupom Fiscal (NFC-e):** Botão **`🧾 NFC-e`** gera a nota fiscal da venda via Focus NFe.
- **Painel de Notas (`/admin/notas-fiscais`):** Visualize todas as notas emitidas com links para **`📄 DANFE (PDF)`** e download do **`📥 XML`** para a contabilidade.

---

### 💵 Estágio 7: Controle Financeiro, Despesas & Fechamento de Caixa
No menu **`💵 Fluxo de Caixa`**:
- **Entradas Automáticas:** Vendas e consertos de OS entregues são somados automaticamente nas receitas do mês.
- **Despesas Operacionais:** Lance gastos com aluguel, energia, internet, peças de reposição, salários e impostos.
- **Lucro Líquido Real:** O sistema calcula em tempo real: `Entradas − Despesas = Lucro Líquido`.
- **Contas a Pagar:** Alertas visuais de contas com vencimento pendente.

---

### 🏢 Estágio 8: Gestão de Filiais, Equipe & Troca de Unidade
No menu **`🏢 Filiais & Unidades`** e **`👥 Usuários & Equipe`**:
- **Cadastrar Filial:** Crie novas unidades com opção de clonar automaticamente o catálogo da matriz.
- **Vínculo de Colaboradores:** No cadastro de usuários, defina a qual filial o técnico ou atendente pertence.
- **Alternar Contexto (`Acessar Painel ➔`):** O administrador alterna entre as unidades da rede com 1 clique.
- **Comparativo de Vendas no Dashboard:** Tabela em tempo real comparando faturamento de balcão, serviços de OS e quantidade de pedidos de cada filial.
- **Seletor de Filiais no Catálogo:** Se houver filiais ativas, o cliente pode alternar a loja no topo do catálogo (`📍 Matriz` / `📍 Filial`).

---

### 👑 Estágio 9: Administração SaaS & Controle de Lojistas (Super Admin)
No menu **`👑 Super Admin`** (`/super-admin/tenants`):
- **Visão Geral:** Listagem de todos os lojistas cadastrados na plataforma com WhatsApp direto para contato.
- **Gestão de Planos & Bloqueio:** Alterne planos (Free, Basic, Pro) ou desative lojas inadimplentes instantaneamente.
- **Notificação de Novos Cadastros:** O Super Admin recebe e-mail de alerta sempre que uma nova loja se registra pela página `/registro`.
- **Fechamento de Cadastros:** Opção nas configurações para permitir ou proibir novos cadastros públicos.

---

## 🔑 Credenciais Padrão (Seed)

| Perfil | E-mail | Senha | Acesso / Painel |
|---|---|---|---|
| **Super Admin (Você)** | `admin@smarttech.com` | `123456` | [http://localhost:8000/super-admin/tenants](http://localhost:8000/super-admin/tenants) |
| **Lojista Smart Tech** | `smarttech@smarttech.com` | `123456` | [http://localhost:8000/admin/dashboard](http://localhost:8000/admin/dashboard) |

---

## 🔄 Alternando entre Modo Loja Única e Modo SaaS

No arquivo `.env`:

### 1. Modo Loja Única Isolada (Padrão Atual)
> Abre direto o catálogo da sua loja na raiz (`/`), sem landing page SaaS, sem link de cadastro público e sem menção a terceiros.
```env
SINGLE_STORE_MODE=true
DEFAULT_STORE_SLUG=smarttech
ALLOW_REGISTRATION=false
```

### 2. Modo Plataforma SaaS
> Exibe a Landing Page na raiz (`/`), permitindo que outros lojistas se cadastrem e tenham seus próprios links (`/loja/nome-da-loja`).
```env
SINGLE_STORE_MODE=false
ALLOW_REGISTRATION=true
```

---

## 🌐 Passo a Passo de Deploy na HostGator (Plano P)

### 1. Envio dos Arquivos
- Envie a pasta `public/` do Laravel para dentro de `public_html/`.
- Envie o restante das pastas (`app/`, `config/`, `vendor/`, etc.) para uma pasta chamada `laravel/` na raiz do cPanel (fora de `public_html`).

### 2. Ajuste do arquivo `public_html/index.php`
Altere as linhas de carregamento para apontar para `../laravel`:
```php
require __DIR__.'/../laravel/vendor/autoload.php';
(require_once __DIR__.'/../laravel/bootstrap/app.php')->handleRequest(Request::capture());
```

### 3. Configuração do `.env`
Preencha os dados do banco de dados MySQL criado no cPanel:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seuusuario_banco
DB_USERNAME=seuusuario_user
DB_PASSWORD=SuaSenhaForte
```

### 4. Execução do Assistente
Abra no seu navegador:
```text
https://seudominio.com.br/install
```
E clique em **"1. Criar Tabelas e Dados Iniciais"** e em **"2. Conectar Pasta de Imagens"**. Pronto! Sua loja estará 100% no ar!

Consulte o guia com fotos e detalhes em: **[`DEPLOY_HOSTGATOR.md`](file:///Users/phelipesc/Documents/projetos/projeto_smarttech/DEPLOY_HOSTGATOR.md)**.

---

## 📁 Estrutura do Projeto

```text
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php   # Dash com Analytics, OS e Vendas
│   │   │   │   ├── ProductController.php     # Produtos com controle de estoque
│   │   │   │   ├── ServiceOrderController.php # Gestão de OS e impressão
│   │   │   │   ├── OrderController.php        # Controle de vendas e pedidos
│   │   │   │   ├── CategoryController.php     # Categorias com emojis
│   │   │   │   └── StoreSettingsController.php # Cores, logo e dados da loja
│   │   │   ├── Auth/                          # Login, Registro e Logout
│   │   │   ├── SuperAdmin/                    # Gestão global de Tenants e Planos
│   │   │   ├── CatalogController.php          # Catálogo público, busca e tracking
│   │   │   └── InstallController.php          # Assistente de implantação Web
│   │   └── Middleware/
│   │       ├── IdentifyTenant.php             # Isolamento de sessão por lojista
│   │       └── SuperAdmin.php                 # Proteção da área super admin
│   ├── Models/                                # Product, ServiceOrder, Order, Tenant, Category, AnalyticsEvent
│   └── Traits/
│       └── BelongsToTenant.php                # Global Scope de isolamento multi-tenant
├── config/
│   └── app.php                                # Configurações de modo isolado / SaaS
├── database/
│   ├── migrations/                            # Schema do banco (Estoque, OS, Analytics, Vendas)
│   └── seeders/                               # Seed inicial com produtos e dados da SmartTech
├── docker/                                    # Configurações Nginx e PHP-FPM local
├── public/
│   ├── css/                                   # catalog.css, admin.css, auth.css, home.css
│   └── images/                                # Logotipos e imagens estáticas
├── resources/
│   └── views/
│       ├── admin/                             # Telas administrativas (OS, Produtos, Vendas, Configurações)
│       ├── auth/                              # Telas de login e registro
│       ├── catalog/                           # Catálogo público e páginas de produto
│       ├── install/                           # Assistente de implantação HostGator
│       ├── layouts/                           # Layouts base (admin, catalog)
│       └── superadmin/                        # Telas do Super Admin
├── routes/
│   └── web.php                                # Rotas públicas, administrativas e de instalação
├── DEPLOY_HOSTGATOR.md                        # Guia detalhado de deploy HostGator
├── docker-compose.yml                         # Orquestração dos containers
└── Dockerfile                                 # Imagem PHP 8.4-FPM
```

---

## 📄 Licença

Este software é proprietário e de uso exclusivo da **FortData** e seus licenciados.
