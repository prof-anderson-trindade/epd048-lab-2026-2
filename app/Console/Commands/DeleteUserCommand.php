<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\ResolvesUser;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;

#[Signature('user:delete {--user= : Id ou e-mail do usuário; sem ele, abre a busca} {--force : Exclui sem pedir confirmação}')]
#[Description('Exclui um usuário pelo terminal')]
final class DeleteUserCommand extends Command
{
    use ResolvesUser;

    public function handle(): int
    {
        $user = $this->resolveUser();

        if ($user === null) {
            return self::FAILURE;
        }

        if (! $this->option('force')) {
            if (! $this->input->isInteractive()) {
                $this->error('Use --force para excluir sem interação.');

                return self::FAILURE;
            }

            if (! confirm(label: "Excluir {$this->userLabel($user)}? Isso não pode ser desfeito.", default: false)) {
                $this->warn('Nada foi alterado.');

                return self::SUCCESS;
            }
        }

        $user->delete();

        $this->info("Usuário {$user->email} excluído.");

        return self::SUCCESS;
    }
}
