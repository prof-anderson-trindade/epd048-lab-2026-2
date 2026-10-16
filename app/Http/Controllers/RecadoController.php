<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecadoRequest;
use App\Models\Recado;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RecadoController extends Controller
{
    /**
     * @return View
     */
    public function index()
    {
        $recados = Recado::with('user')
            ->latest()
            ->get();

        return view('mural', ['recados' => $recados]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(RecadoRequest $request)
    {
        $request->user()->recados()->create($request->validated());

        return redirect()->route('mural');
    }
}
