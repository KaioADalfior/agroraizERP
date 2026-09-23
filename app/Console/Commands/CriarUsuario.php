<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CriarUsuario extends Command
{
    protected $signature = 'agroraiz:usuario
                            {--nome= : Nome do usuário}
                            {--email= : E-mail de acesso}
                            {--senha= : Senha (mínimo 8 caracteres)}';

    protected $description = 'Cria um usuário de acesso ao sistema (ou redefine a senha se o e-mail já existir)';

    public function handle(): int
    {
        $nome = $this->option('nome') ?: $this->ask('Nome');
        $email = $this->option('email') ?: $this->ask('E-mail');
        $senha = $this->option('senha') ?: $this->secret('Senha (mínimo 8 caracteres)');

        $validador = Validator::make(
            ['name' => $nome, 'email' => $email, 'password' => $senha],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
            ],
        );

        if ($validador->fails()) {
            foreach ($validador->errors()->all() as $erro) {
                $this->error($erro);
            }

            return self::FAILURE;
        }

        $usuario = User::updateOrCreate(
            ['email' => $email],
            ['name' => $nome, 'password' => $senha],
        );

        $this->info($usuario->wasRecentlyCreated
            ? "Usuário {$usuario->email} criado com sucesso."
            : "Usuário {$usuario->email} atualizado (nome e senha redefinidos)."
        );

        return self::SUCCESS;
    }
}
