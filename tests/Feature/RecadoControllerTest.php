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

test('script tags typed in a recado are escaped and never rendered as html', function () {
    $user = User::factory()->create();
    $payload = '<script>alert("xss")</script>';

    $this->actingAs($user)
        ->post(route('mural.store'), ['texto' => $payload])
        ->assertRedirect(route('mural'));

    $content = $this->get(route('mural'))->getContent();

    expect($content)
        ->toContain('&lt;script&gt;')
        ->not->toContain($payload);
});

test('a destaque field posted with a valid recado is ignored and not persisted', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('mural.store'), [
            'texto' => 'Recado legítimo',
            'destaque' => '1',
        ])
        ->assertRedirect(route('mural'));

    $recado = Recado::first();

    expect($recado)
        ->not->toBeNull()
        ->texto->toBe('Recado legítimo')
        ->destaque->toBeFalse();
});

test('the owner can delete their own recado', function () {
    $owner = User::factory()->create();
    $recado = Recado::factory()->for($owner)->create();

    $this->actingAs($owner)
        ->delete(route('mural.destroy', $recado))
        ->assertRedirect(route('mural'));

    expect(Recado::find($recado->id))->toBeNull();
});

test('another user cannot delete someone else recado and gets a 403', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $recado = Recado::factory()->for($owner)->create();

    $this->actingAs($other)
        ->delete(route('mural.destroy', $recado))
        ->assertForbidden();

    expect(Recado::find($recado->id))->not->toBeNull()
        ->and($recado->fresh()->user_id)->toBe($owner->id);
});
