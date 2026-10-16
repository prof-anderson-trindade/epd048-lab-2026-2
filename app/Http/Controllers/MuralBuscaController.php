<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MuralBuscaController extends Controller
{
    /**
     * @return View
     */
    public function index(Request $request)
    {
        $termo = (string) $request->query('q', '');
        $sql = '';
        $bindings = [];
        $resultados = collect();

        if ($termo !== '') {
            $query = $request->user()->recados()
                ->where('texto', 'like', '%'.$termo.'%')
                ->latest();

            $sql = $query->toSql();
            $bindings = $query->getBindings();
            $resultados = $query->get(['texto', 'created_at']);
        }

        return view('mural.busca', [
            'termo' => $termo,
            'sql' => $sql,
            'bindings' => $bindings,
            'resultados' => $resultados,
        ]);
    }
}
