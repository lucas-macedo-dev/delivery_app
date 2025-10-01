<!DOCTYPE html>
<html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #4f46e5; color: white; padding: 20px; text-align: center; }
            .content { background: #f9fafb; padding: 20px; margin: 20px 0; }
            .button { display: inline-block; padding: 12px 24px; margin: 10px 5px; text-decoration: none; border-radius: 5px; font-weight: bold; }
            .approve { background: #10b981; color: white; }
            .reject { background: #ef4444; color: white; }
            .info { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #4f46e5; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Nova Solicitação de Registro</h1>
            </div>

            <div class="content">
                <h2>Um novo usuário solicitou acesso ao sistema:</h2>

                <div class="info">
                    <p><strong>Nome:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Data do registro:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <p>Por favor, revise esta solicitação e tome uma ação:</p>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $approvalUrl }}" class="button approve">✓ Aprovar Usuário</a>
                    <a href="{{ $rejectUrl }}" class="button reject">✗ Rejeitar Usuário</a>
                </div>

                <p style="color: #6b7280; font-size: 14px;">
                    <strong>Nota:</strong> Estes links são válidos por 72 horas por questões de segurança.
                </p>
            </div>
        </div>
    </body>
</html>
