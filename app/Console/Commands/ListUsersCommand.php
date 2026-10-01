<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

use function Laravel\Prompts\datatable;
use function Laravel\Prompts\select;

/**
 * No terminal, abre uma tabela navegável (setas, PageUp/PageDown, `/` para filtrar) e, ao escolher um
 * usuário, oferece alterar a senha ou excluir pelos comandos user:password e user:delete.
 * Sem interação (`-n`) ou sem TTY na entrada, imprime a tabela estática limitada por --limit.
 */
#[Signature('user:list {search? : Nome ou e-mail} {--limit=50 : Máximo de linhas da tabela sem interação}')]
#[Description('Lista os usuários e permite alterar a senha ou excluir')]
final class ListUsersCommand extends Command
{
    private const string BACK = 'back';

    private const string EXIT = 'exit';

    public function handle(): int
    {
        if (! $this->browsable()) {
            return $this->printTable();
        }

        while (true) {
            $users = $this->query()->get();

            if ($users->isEmpty()) {
                $this->info('Nenhum usuário encontrado.');

                return self::SUCCESS;
            }

            $userId = datatable(
                headers: $this->headers(),
                rows: $users->mapWithKeys(fn (User $user): array => [$user->id => $this->row($user)])->all(),
                scroll: 15,
                label: "Usuários ({$users->count()})",
                hint: 'Enter escolhe o usuário · / filtra · Ctrl+C sai',
            );

            $user = $users->find($userId);
            $action = $this->chooseAction($user);

            if ($action === self::EXIT) {
                return self::SUCCESS;
            }

            if ($action !== self::BACK) {
                $this->call($action, ['--user' => (string) $user->id]);
            }
        }
    }

    /**
     * Sem TTY o datatable não espera teclas e devolveria a primeira linha; nos testes o terminal é simulado.
     */
    private function browsable(): bool
    {
        return $this->input->isInteractive() && (stream_isatty(STDIN) || $this->laravel->runningUnitTests());
    }

    /**
     * @return Builder<User>
     */
    private function query(): Builder
    {
        $search = $this->argument('search');

        return User::query()
            ->when(is_string($search) && trim($search) !== '', fn (Builder $query): Builder => $query
                ->where(fn (Builder $match): Builder => $match
                    ->where('name', 'like', '%'.trim($search).'%')
                    ->orWhere('email', 'like', '%'.trim($search).'%')))
            ->orderBy('id');
    }

    private function printTable(): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $users = $this->query()->limit($limit + 1)->get();

        if ($users->isEmpty()) {
            $this->info('Nenhum usuário encontrado.');

            return self::SUCCESS;
        }

        $this->table($this->headers(), $users->take($limit)->map(fn (User $user): array => $this->row($user))->all());

        if ($users->count() > $limit) {
            $this->warn("Mostrando os primeiros {$limit}; refine a busca ou aumente --limit.");
        }

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function headers(): array
    {
        return ['ID', 'Nome', 'E-mail', 'E-mail verificado', '2FA', 'Criado em'];
    }

    /**
     * @return array<int, string>
     */
    private function row(User $user): array
    {
        return [
            (string) $user->id,
            $user->name,
            $user->email,
            $user->email_verified_at?->format('d/m/Y H:i') ?? '—',
            $user->two_factor_confirmed_at !== null ? 'ativo' : '—',
            $user->created_at?->format('d/m/Y H:i') ?? '—',
        ];
    }

    /**
     * Ação sobre o usuário escolhido: o nome do comando a chamar, voltar à lista ou sair.
     */
    private function chooseAction(User $user): string
    {
        return (string) select(
            label: "{$user->name} — {$user->email} (#{$user->id})",
            options: [
                'user:password' => 'Alterar a senha',
                'user:delete' => 'Excluir o usuário',
                self::BACK => 'Voltar à lista',
                self::EXIT => 'Sair',
            ],
            default: self::BACK,
        );
    }
}
