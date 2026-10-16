<?php

use App\Models\Recado;
use App\Models\User;

test('a logged-in user finds their own recado by a term that appears in the text', function () {
    $user = User::factory()->create();
    Recado::factory()->for($user)->create(['texto' => 'Hoje tem reunião às 10h']);
    Recado::factory()->for($user)->create(['texto' => 'Comprar pão']);

    $this->actingAs($user)
        ->get(route('mural.busca', ['q' => 'reunião']))
        ->assertOk()
        ->assertSee('Hoje tem reunião às 10h')
        ->assertDontSee('Comprar pão');
});

test('a sql injection attempt with OR 1=1 does not leak recados from another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Recado::factory()->for($user)->create(['texto' => 'reunião de condomínio']);
    Recado::factory()->for($other)->create(['texto' => 'anotação privada do vizinho']);

    $this->actingAs($user)
        ->get(route('mural.busca', ['q' => "%' OR 1=1 --"]))
        ->assertOk()
        ->assertDontSee('anotação privada do vizinho');
});

test('a union based injection attempting to leak user emails and passwords does not succeed', function () {
    $user = User::factory()->create();
    $other = User::factory()->create(['email' => 'vitima@example.test']);

    $this->actingAs($user)
        ->get(route('mural.busca', ['q' => "xyz' UNION SELECT email, password FROM users --"]))
        ->assertOk()
        ->assertDontSee('vitima@example.test');
});
