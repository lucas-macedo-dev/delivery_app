<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature   = 'make:admin {email?} {name?}';
    protected $description = 'Criar um usuário administrador';

    public function handle(): int
    {
        $email    = $this->argument('email') ?? $this->ask('Email do administrador:');
        $name     = $this->argument('name') ?? $this->ask('Nome do administrador:');
        $password = $this->secret('Senha:');

        $user = User::create([
            'name'        => $name,
            'email'       => $email,
            'password'    => Hash::make($password),
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        $this->info("Administrador criado com sucesso!");
        $this->info("Email: {$email}");

        // Atualizar o .env com o email do admin
        // $this->updateEnvFile('ADMIN_EMAIL', $email);

        return Command::SUCCESS;
    }

    private function updateEnvFile($key, $value): void
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            file_put_contents(
                $path, str_replace(
                $key . '=' . env($key),
                $key . '=' . $value,
                file_get_contents($path)
            )
            );
        }
    }
}
