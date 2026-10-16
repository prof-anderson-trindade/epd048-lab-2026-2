<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $ana = User::factory()->create([
            'name' => 'Ana',
            'email' => 'ana@mural.test',
        ]);

        $bruno = User::factory()->create([
            'name' => 'Bruno',
            'email' => 'bruno@mural.test',
        ]);

        $ana->recados()->createMany([
            ['texto' => 'Reunião do grupo amanhã às 14h, na sala 12.'],
            ['texto' => 'Alguém tem o link da aula de ontem?'],
        ]);

        $bruno->recados()->createMany([
            ['texto' => 'Vou levar bolo de cenoura para a reunião.'],
            ['texto' => 'Lembrete: a entrega do trabalho é na sexta.'],
        ]);
    }
}
