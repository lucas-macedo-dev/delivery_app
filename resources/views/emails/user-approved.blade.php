<!DOCTYPE html>
<html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #10b981; color: white; padding: 20px; text-align: center; }
            .content { background: #f9fafb; padding: 20px; margin: 20px 0; }
            .button { display: inline-block; padding: 12px 24px; margin: 20px 0; background: #4f46e5; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>✓ Conta Aprovada!</h1>
            </div>

            <div class="content">
                <h2>Olá {{ $user->name }},</h2>

                <p>Temos o prazer de informar que sua conta foi <strong>aprovada com sucesso</strong>!</p>

                <p>Você já pode acessar o sistema utilizando suas credenciais de login.</p>

                <div style="text-align: center;">
                    <a href="{{ route('login') }}" class="button">Acessar o Sistema</a>
                </div>

                <p>Se você tiver alguma dúvida, não hesite em entrar em contato conosco.</p>

                <p>Atenciosamente,<br> {{ config('app.name') }}</p>
            </div>
        </div>
    </body>
</html>
