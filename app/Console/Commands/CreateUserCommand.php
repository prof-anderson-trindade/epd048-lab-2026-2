<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('user:create')]
#[Description('Cria um usuário (nome, e-mail e senha) pelo terminal')]
class CreateUserCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = text(
            label: 'Nome',
            required: true,
            validate: ['name' => 'max:255'],
        );

        $email = text(
            label: 'E-mail',
            required: true,
            validate: ['email' => 'email|max:255|unique:users,email'],
        );

        $plainPassword = password(
            label: 'Senha',
            required: true,
            validate: ['password' => [Password::default()]],
        );

        password(
            label: 'Confirme a senha',
            required: true,
            validate: fn (string $value): ?string => $value === $plainPassword
                ? null
                : 'As senhas não conferem.',
        );

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $plainPassword,
        ]);

        info("Usuário {$user->email} criado com sucesso.");

        return self::SUCCESS;
    }
}
