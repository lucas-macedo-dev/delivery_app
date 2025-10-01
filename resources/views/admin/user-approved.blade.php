<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Usuário Aprovado</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0;
            }
            .container {
                background: white;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
                text-align: center;
                max-width: 500px;
            }
            .success-icon {
                width: 80px;
                height: 80px;
                background: #10b981;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
            }
            .success-icon::before {
                content: "✓";
                color: white;
                font-size: 40px;
                font-weight: bold;
            }
            h1 { color: #1f2937; margin-bottom: 10px; }
            p { color: #6b7280; line-height: 1.6; }
            .user-info {
                background: #f3f4f6;
                padding: 15px;
                border-radius: 5px;
                margin: 20px 0;
                text-align: left;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="success-icon"></div>
            <h1>Usuário Aprovado com Sucesso!</h1>
            <p>O usuário foi aprovado e notificado por email.</p>

            <div class="user-info">
                <p><strong>Nome:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Data de aprovação:</strong> {{ now()->format('d/m/Y H:i') }}</p>
            </div>

            <p>O usuário já pode acessar o sistema com suas credenciais.</p>
        </div>
    </body>
</html>
