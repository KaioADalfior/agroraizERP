<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Propriedade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PropriedadeController extends Controller
{
    public function store(Request $request, Cliente $cliente): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'area_hectares' => ['nullable', 'numeric', 'min:0'],
            'localizacao' => ['nullable', 'string', 'max:255'],
            'observacao' => ['nullable', 'string', 'max:2000'],
        ]);

        $cliente->propriedades()->create($dados);

        return redirect()->route('clientes.show', $cliente)->with('sucesso', 'Propriedade cadastrada com sucesso.');
    }

    public function destroy(Propriedade $propriedade): RedirectResponse
    {
        $cliente = $propriedade->cliente_id;

        $propriedade->delete();

        return redirect()->route('clientes.show', $cliente)->with('sucesso', 'Propriedade removida.');
    }
}
