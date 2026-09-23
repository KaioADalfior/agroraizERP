<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $busca = trim((string) $request->query('busca', ''));

        $clientes = Cliente::query()
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($query) use ($busca) {
                    $query->where('nome', 'like', "%{$busca}%")
                        ->orWhere('cidade_estado', 'like', "%{$busca}%");
                });
            })
            ->orderBy('nome')
            ->paginate(15)
            ->withQueryString();

        return view('clientes.index', [
            'clientes' => $clientes,
            'busca' => $busca,
        ]);
    }

    public function create(): View
    {
        return view('clientes.create', [
            'cliente' => new Cliente(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Cliente::create($this->validado($request));

        return redirect()->route('clientes.index')->with('sucesso', 'Cliente cadastrado com sucesso.');
    }

    public function edit(Cliente $cliente): View
    {
        return view('clientes.edit', [
            'cliente' => $cliente,
        ]);
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($this->validado($request));

        return redirect()->route('clientes.index')->with('sucesso', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('sucesso', 'Cliente removido.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validado(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cidade_estado' => ['nullable', 'string', 'max:255'],
            'data' => ['nullable', 'date'],
            'observacao' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
