<!DOCTYPE html>
<html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #ef4444; color: white; padding: 20px; text-align: center; }
            .content { background: #f9fafb; padding: 20px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Solicitação de Registro</h1>
            </div>

            <div class="content">
                <h2>Olá {{ $userName }},</h2>

                <p>Infelizmente, sua solicitação de registro não foi aprovada neste momento.</p>

                <p>Se você acredita que isso foi um erro ou gostaria de mais informações,
                    entre em contato com o administrador do sistema.</p>

                <p>Atenciosamente,<br>
                    {{ config('app.name') }}</p>
            </div>
        </div>
    </body>
</html>
