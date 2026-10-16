<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class OlaController extends Controller
{
    public function __invoke(?string $nome = null): View
    {
        return view('ola', [
            'nome' => $nome ?? 'visitante',
            'agora' => now()->format('H:i:s'),
        ]);
    }
}
