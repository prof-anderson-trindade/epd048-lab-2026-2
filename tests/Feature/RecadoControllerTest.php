<?php

use App\Models\Recado;
use App\Models\User;

test('guests are redirected to the login page when visiting the mural', function () {
    $this->get(route('mural'))
        ->assertRedirect(route('login'));
});

test('a logged-in user can post a valid recado', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('mural.store'), ['texto' => 'Olá, mural!'])
        ->assertRedirect(route('mural'));

    $recado = Recado::first();

    expect($recado)
        ->not->toBeNull()
        ->texto->toBe('Olá, mural!')
        ->user_id->toBe($user->id);
});

test('an empty recado is rejected with a validation error', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('mural'))
        ->post(route('mural.store'), ['texto' => ''])
        ->assertRedirect(route('mural'))
        ->assertSessionHasErrors('texto', 'branco');

    expect(Recado::count())->toBe(0);
});

test('a recado longer than 280 characters is rejected', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('mural'))
        ->post(route('mural.store'), ['texto' => str_repeat('a', 281)])
        ->assertRedirect(route('mural'))
        ->assertSessionHasErrors('texto', '280');

    expect(Recado::count())->toBe(0);
});
