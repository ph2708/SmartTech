<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | SmartCatálogo</title>
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
                <h1>Bem-vindo de volta!</h1>
                <p>Entre para gerenciar sua loja</p>
            </div>

            @if($errors->any())
            <div class="auth-error">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="seu@email.com">
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>

                <div class="form-check">
                    <label>
                        <input type="checkbox" name="remember">
                        <span>Lembrar de mim</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Entrar</button>
            </form>

            @if(config('app.allow_registration', true))
            <div class="auth-footer">
                <p>Não tem uma conta? <a href="{{ route('register') }}">Criar conta grátis</a></p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
