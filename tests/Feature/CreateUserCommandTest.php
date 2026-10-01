<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;

test('creates a user from the terminal prompts', function () {
    $this->artisan('user:create')
        ->expectsQuestion('Nome', 'Maria Silva')
        ->expectsQuestion('E-mail', 'maria@example.com')
        ->expectsQuestion('Senha', 'password-123456')
        ->expectsQuestion('Confirme a senha', 'password-123456')
        ->assertSuccessful();

    $user = User::where('email', 'maria@example.com')->firstOrFail();

    expect($user->name)->toBe('Maria Silva')
        ->and(Hash::check('password-123456', $user->password))->toBeTrue();
});

test('rejects an email that is already registered', function () {
    User::factory()->create(['email' => 'maria@example.com']);

    $this->artisan('user:create')
        ->expectsQuestion('Nome', 'Maria Silva')
        ->expectsQuestion('E-mail', 'maria@example.com')
        ->assertFailed();

    expect(User::count())->toBe(1);
});

test('lists users in a static table without interaction', function () {
    User::factory()->create(['name' => 'Maria Silva', 'email' => 'maria@example.com']);
    User::factory()->create(['name' => 'João Souza', 'email' => 'joao@example.com']);

    $this->artisan('user:list', ['--no-interaction' => true])
        ->expectsOutputToContain('maria@example.com')
        ->expectsOutputToContain('joao@example.com')
        ->assertSuccessful();
});

test('filters the list by name or email and warns when it exceeds the limit', function () {
    User::factory()->create(['name' => 'Maria Silva', 'email' => 'maria@example.com']);
    User::factory()->create(['name' => 'João Souza', 'email' => 'joao@example.com']);

    $this->artisan('user:list', ['search' => 'joao', '--no-interaction' => true])
        ->expectsOutputToContain('joao@example.com')
        ->doesntExpectOutputToContain('maria@example.com')
        ->assertSuccessful();

    $this->artisan('user:list', ['--limit' => 1, '--no-interaction' => true])
        ->expectsOutputToContain('Mostrando os primeiros 1')
        ->assertSuccessful();
});

test('changes the password of the chosen user', function () {
    $user = User::factory()->create(['email' => 'maria@example.com']);

    $this->artisan('user:password', ['--user' => 'maria@example.com'])
        ->expectsQuestion('Nova senha para '.$user->name." — maria@example.com (#{$user->id})", 'new-password-123')
        ->expectsQuestion('Confirme a nova senha', 'new-password-123')
        ->assertSuccessful();

    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});

test('fails to change the password for an unknown user', function () {
    $this->artisan('user:password', ['--user' => '999'])
        ->expectsOutputToContain('Usuário não encontrado')
        ->assertFailed();
});

test('deletes a user after confirmation', function () {
    $user = User::factory()->create();

    $this->artisan('user:delete', ['--user' => (string) $user->id])
        ->expectsConfirmation("Excluir {$user->name} — {$user->email} (#{$user->id})? Isso não pode ser desfeito.", 'yes')
        ->assertSuccessful();

    expect(User::find($user->id))->toBeNull();
});

test('keeps the user when the deletion is declined', function () {
    $user = User::factory()->create();

    $this->artisan('user:delete', ['--user' => (string) $user->id])
        ->expectsConfirmation("Excluir {$user->name} — {$user->email} (#{$user->id})? Isso não pode ser desfeito.", 'no')
        ->assertSuccessful();

    expect(User::find($user->id))->not->toBeNull();
});

test('requires --force to delete without interaction', function () {
    $user = User::factory()->create();

    $this->artisan('user:delete', ['--user' => (string) $user->id, '--no-interaction' => true])
        ->assertFailed();
    expect(User::find($user->id))->not->toBeNull();

    $this->artisan('user:delete', ['--user' => (string) $user->id, '--force' => true, '--no-interaction' => true])
        ->assertSuccessful();
    expect(User::find($user->id))->toBeNull();
});

test('browses the table and exits from the action menu', function () {
    $user = User::factory()->create();

    Prompt::fake([Key::ENTER]);

    $this->artisan('user:list')
        ->expectsChoice("{$user->name} — {$user->email} (#{$user->id})", 'exit', [
            'user:password' => 'Alterar a senha',
            'user:delete' => 'Excluir o usuário',
            'back' => 'Voltar à lista',
            'exit' => 'Sair',
        ])
        ->assertSuccessful();
});

test('changes the password of the chosen row without asking for the user again', function () {
    $user = User::factory()->create();
    $label = "{$user->name} — {$user->email} (#{$user->id})";

    Prompt::fake([Key::ENTER, Key::ENTER]);

    $this->artisan('user:list')
        ->expectsChoice($label, 'user:password', [
            'user:password' => 'Alterar a senha',
            'user:delete' => 'Excluir o usuário',
            'back' => 'Voltar à lista',
            'exit' => 'Sair',
        ])
        ->expectsQuestion("Nova senha para {$label}", 'new-password-123')
        ->expectsQuestion('Confirme a nova senha', 'new-password-123')
        ->expectsChoice($label, 'exit', [
            'user:password' => 'Alterar a senha',
            'user:delete' => 'Excluir o usuário',
            'back' => 'Voltar à lista',
            'exit' => 'Sair',
        ])
        ->assertSuccessful();

    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});
