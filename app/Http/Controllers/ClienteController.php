<?php

namespace App\Http\Controllers;

use App\Models\AnaliseSolo;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
                        ->orWhere('cidade', 'like', "%{$busca}%")
                        ->orWhere('bairro', 'like', "%{$busca}%")
                        ->orWhere('cep', 'like', "%{$busca}%");
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

    public function show(Cliente $cliente): View
    {
        $cliente->load(['propriedades' => function ($query) {
            $query->orderBy('nome');
        }]);

        $analises = AnaliseSolo::query()
            ->whereHas('propriedade', function ($query) use ($cliente) {
                $query->where('cliente_id', $cliente->id);
            })
            ->with(['propriedade', 'cultura'])
            ->orderByDesc('data_coleta')
            ->get();

        return view('clientes.show', [
            'cliente' => $cliente,
            'analises' => $analises,
        ]);
    }

    public function create(): View
    {
        return view('clientes.create', [
            'cliente' => new Cliente(),
            'estados' => Cliente::ESTADOS,
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
            'estados' => Cliente::ESTADOS,
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
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cep' => ['required', 'string', 'regex:/^\d{5}-?\d{3}$/'],
            'endereco' => ['required', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'string', Rule::in(array_keys(Cliente::ESTADOS))],
            'data' => ['nullable', 'date'],
            'observacao' => ['nullable', 'string', 'max:2000'],
        ]);

        // Normaliza o CEP sempre para o formato 00000-000, aceitando os dois
        // formatos (com ou sem traço) vindos do formulário.
        $cepLimpo = preg_replace('/\D/', '', $dados['cep']);
        $dados['cep'] = substr($cepLimpo, 0, 5).'-'.substr($cepLimpo, 5);

        return $dados;
    }
}
