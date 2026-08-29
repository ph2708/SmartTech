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
                <h1>Crie sua loja grátis!</h1>
                <p>Em menos de 2 minutos sua loja estará no ar</p>
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
                    <label for="name">Seu nome</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="João Silva">
                </div>

                <div class="form-group">
                    <label for="store_name">Nome da sua loja</label>
                    <input type="text" id="store_name" name="store_name" value="{{ old('store_name') }}" required placeholder="Ex: Smart Tech">
                </div>

                <div class="form-group">
                    <label for="whatsapp">WhatsApp (com DDD)</label>
                    <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" required placeholder="64 99249-5817">
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="seu@email.com">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Senha</label>
                        <input type="password" id="password" name="password" required placeholder="Mínimo 6 caracteres">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirmar senha</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repita a senha">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Criar Minha Loja 🚀</button>
            </form>

            <div class="auth-footer">
                <p>Já tem uma conta? <a href="{{ route('login') }}">Faça login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
