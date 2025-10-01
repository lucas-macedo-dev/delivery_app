<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Usuário Rejeitado</title>
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
            .reject-icon {
                width: 80px;
                height: 80px;
                background: #ef4444;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
            }
            .reject-icon::before {
                content: "✗";
                color: white;
                font-size: 40px;
                font-weight: bold;
            }
            h1 { color: #1f2937; margin-bottom: 10px; }
            p { color: #6b7280; line-height: 1.6; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="reject-icon"></div>
            <h1>Usuário Rejeitado</h1>
            <p>O registro do usuário <strong>{{ $userName }}</strong> foi rejeitado e removido do sistema.</p>
            <p>Um email de notificação foi enviado ao usuário.</p>
        </div>
    </body>
</html>
