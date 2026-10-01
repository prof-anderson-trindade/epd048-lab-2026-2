<?php

namespace App\Console\Commands\Concerns;

use App\Models\User;

use function Laravel\Prompts\search;

/**
 * Localiza o usuário-alvo de um comando pela opção --user (id ou e-mail) ou por uma busca interativa.
 */
trait ResolvesUser
{
    private const int SEARCH_MINIMUM_LENGTH = 2;

    private const int SEARCH_LIMIT = 10;

    private function resolveUser(): ?User
    {
        $identifier = $this->option('user');

        if (! is_string($identifier) || $identifier === '') {
            if (! $this->input->isInteractive()) {
                $this->error('A opção --user (id ou e-mail) é obrigatória quando o comando roda sem interação.');

                return null;
            }

            $identifier = (string) search(
                label: 'Buscar usuário',
                options: fn (string $term): array => $this->searchOptions($term),
                placeholder: 'Nome ou e-mail',
                hint: 'Digite ao menos '.self::SEARCH_MINIMUM_LENGTH.' caracteres.',
            );
        }

        $user = User::query()
            ->where(is_numeric($identifier) ? 'id' : 'email', $identifier)
            ->first();

        if (! $user instanceof User) {
            $this->error('Usuário não encontrado. Confira --user.');

            return null;
        }

        return $user;
    }

    /**
     * @return array<int, string>
     */
    private function searchOptions(string $term): array
    {
        $term = trim($term);

        if (mb_strlen($term) < self::SEARCH_MINIMUM_LENGTH) {
            return [];
        }

        return User::query()
            ->where(fn ($query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%"))
            ->orderBy('name')
            ->orderBy('id')
            ->limit(self::SEARCH_LIMIT)
            ->get()
            ->mapWithKeys(fn (User $user): array => [$user->id => $this->userLabel($user)])
            ->all();
    }

    private function userLabel(User $user): string
    {
        return "{$user->name} — {$user->email} (#{$user->id})";
    }
}
