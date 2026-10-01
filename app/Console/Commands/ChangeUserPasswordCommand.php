<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\ResolvesUser;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;

#[Signature('user:password {--user= : Id ou e-mail do usuário; sem ele, abre a busca}')]
#[Description('Altera a senha de um usuário pelo terminal')]
final class ChangeUserPasswordCommand extends Command
{
    use ResolvesUser;

    public function handle(): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('A nova senha é pedida no terminal; rode o comando com interação.');

            return self::FAILURE;
        }

        $user = $this->resolveUser();

        if ($user === null) {
            return self::FAILURE;
        }

        $newPassword = password(
            label: "Nova senha para {$this->userLabel($user)}",
            required: true,
            validate: ['password' => [Password::default()]],
        );

        password(
            label: 'Confirme a nova senha',
            required: true,
            validate: fn (string $value): ?string => $value === $newPassword
                ? null
                : 'As senhas não conferem.',
        );

        $user->forceFill(['password' => $newPassword])->save();

        $this->info("Senha de {$user->email} alterada com sucesso.");

        return self::SUCCESS;
    }
}
