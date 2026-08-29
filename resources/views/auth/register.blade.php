<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta | SmartCatálogo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="{{ route('home') }}" class="auth-logo">
                    <span class="logo-icon">⚡</span>
                    <span>SmartCatálogo</span>
                </a>
                <h1>Solicitar Meu Catálogo & Orçamento</h1>
                <p>Preencha os dados abaixo para nossa equipe ativar sua plataforma</p>
            </div>

            @if($errors->any())
            <div class="auth-error">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="name">Seu Nome / Responsável *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Ex: João Silva">
                </div>

                <div class="form-group">
                    <label for="store_name">Nome da sua Loja / Assistência *</label>
                    <input type="text" id="store_name" name="store_name" value="{{ old('store_name') }}" required placeholder="Ex: Smart Tech Assistência">
                </div>

                <div class="form-group">
                    <label for="whatsapp">WhatsApp Comercial (com DDD) *</label>
                    <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" required placeholder="Ex: 64 99249-5817">
                    <small style="color: #64748b; font-size: 0.8rem; margin-top: 4px; display: block;">Número onde você receberá os pedidos dos clientes</small>
                </div>

                <div class="form-group">
                    <label for="email">E-mail para Acesso ao Painel *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="seu@email.com">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Defina sua Senha *</label>
                        <input type="password" id="password" name="password" required placeholder="Mínimo 6 caracteres">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Senha *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repita a senha">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full" style="padding: 14px; font-weight: 800; font-size: 1rem;">
                    🚀 Enviar Solicitação & Criar Meu Catálogo
                </button>
            </form>

            <div class="auth-footer">
                <p>Já possui acesso como lojista? <a href="{{ route('login') }}" style="font-weight: 700; color: #e63946;">Entrar no Painel</a></p>
            </div>
        </div>
    </div>
</body>
</html>
