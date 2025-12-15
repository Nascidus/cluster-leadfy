<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <title>Login · Dashboard GA4</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
        <style>
            :root {
                --bg: #FFF9ED;
                --card: #ffffff;
                --accent: #DAFF01;
                --text: #1b1b1b;
                --muted: #6b7280;
            }
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            body {
                font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "SF Pro Text", "Helvetica Neue", sans-serif;
                background-color: var(--bg);
                color: var(--text);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px 16px;
            }
            .shell {
                width: 100%;
                max-width: 380px;
            }
            .card {
                background: var(--card);
                border-radius: 18px;
                border: 1px solid rgba(0, 0, 0, 0.04);
                box-shadow:
                    0 18px 45px rgba(0, 0, 0, 0.04),
                    0 0 0 1px rgba(255, 255, 255, 0.9);
                padding: 24px 24px 22px;
            }
            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 10px;
                border-radius: 999px;
                border: 1px solid rgba(27, 27, 27, 0.12);
                background: #FFF9ED;
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                color: var(--muted);
            }
            .eyebrow-dot {
                width: 7px;
                height: 7px;
                border-radius: 999px;
                background: radial-gradient(circle at 30% 30%, #ffffff, var(--accent));
                box-shadow: 0 0 0 4px rgba(218, 255, 1, 0.25);
            }
            h1 {
                font-size: 22px;
                font-weight: 600;
                letter-spacing: -0.03em;
                margin: 12px 0 4px;
            }
            p.subtitle {
                font-size: 13px;
                color: var(--muted);
                margin-bottom: 16px;
            }
            label {
                display: block;
                font-size: 12px;
                font-weight: 500;
                margin-bottom: 4px;
                color: var(--text);
            }
            input[type="text"],
            input[type="password"] {
                width: 100%;
                padding: 8px 10px;
                border-radius: 10px;
                border: 1px solid rgba(27, 27, 27, 0.12);
                font-size: 13px;
                outline: none;
                background: #FDFBF4;
            }
            input[type="text"]:focus,
            input[type="password"]:focus {
                border-color: var(--accent);
                box-shadow: 0 0 0 1px rgba(218, 255, 1, 0.4);
            }
            .field {
                margin-bottom: 12px;
            }
            .btn {
                width: 100%;
                border: none;
                border-radius: 999px;
                padding: 9px 14px;
                font-size: 13px;
                font-weight: 500;
                cursor: pointer;
                background: linear-gradient(135deg, #1b1b1b, #373B40);
                color: #FFF9ED;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .btn span.dot {
                width: 6px;
                height: 6px;
                border-radius: 999px;
                background: var(--accent);
            }
            .error {
                font-size: 12px;
                color: #b91c1c;
                margin-top: 4px;
            }
            .flash {
                font-size: 12px;
                color: #b91c1c;
                margin-bottom: 10px;
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <div class="card">
                <div class="eyebrow">
                    <div class="eyebrow-dot"></div>
                    <span>GA4 Dashboard</span>
                </div>
                <h1>Login de acesso</h1>
                <p class="subtitle">
                    Acesso restrito ao dashboard interno de métricas GA4.
                    Use as credenciais configuradas no servidor.
                </p>

                @if(session('error'))
                    <div class="flash">{{ session('error') }}</div>
                @endif
                @if($errors->has('auth'))
                    <div class="flash">{{ $errors->first('auth') }}</div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="field">
                        <label for="username">Usuário</label>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            autocomplete="username"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="password">Senha</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <button type="submit" class="btn">
                        <span class="dot"></span>
                        <span>Entrar no dashboard</span>
                    </button>
                </form>
            </div>
        </div>
    </body>
</html>


