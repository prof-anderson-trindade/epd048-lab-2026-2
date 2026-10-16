<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecadoRequest;
use App\Models\Recado;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RecadoController extends Controller
{
    use AuthorizesRequests;

    /**
     * @return View
     */
    public function index()
    {
        $recados = Recado::with('user')
            ->orderByDesc('destaque')
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

    /**
     * @return RedirectResponse
     */
    public function destroy(Recado $recado)
    {
        $this->authorize('delete', $recado);

        $recado->delete();

        return redirect()->route('mural');
    }
}
