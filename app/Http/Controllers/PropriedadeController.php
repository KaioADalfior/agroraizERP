<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Propriedade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PropriedadeController extends Controller
{
    public function store(Request $request, Cliente $cliente): RedirectResponse|JsonResponse
    {
        $propriedade = $cliente->propriedades()->create($this->validado($request));

        if ($request->wantsJson()) {
            return response()->json([
                'sucesso' => 'Propriedade cadastrada com sucesso.',
                'propriedade' => $propriedade,
            ], 201);
        }

        return redirect()->route('clientes.edit', ['cliente' => $cliente, 'aba' => 'propriedades'])->with('sucesso', 'Propriedade cadastrada com sucesso.');
    }

    public function update(Request $request, Propriedade $propriedade): RedirectResponse|JsonResponse
    {
        $propriedade->update($this->validado($request));

        if ($request->wantsJson()) {
            return response()->json([
                'sucesso' => 'Propriedade atualizada com sucesso.',
                'propriedade' => $propriedade->fresh(),
            ]);
        }

        return redirect()->route('clientes.edit', ['cliente' => $propriedade->cliente_id, 'aba' => 'propriedades'])->with('sucesso', 'Propriedade atualizada com sucesso.');
    }

    public function destroy(Request $request, Propriedade $propriedade): RedirectResponse|JsonResponse
    {
        $clienteId = $propriedade->cliente_id;

        $propriedade->delete();

        if ($request->wantsJson()) {
            return response()->json(['sucesso' => 'Propriedade removida.']);
        }

        return redirect()->route('clientes.edit', ['cliente' => $clienteId, 'aba' => 'propriedades'])->with('sucesso', 'Propriedade removida.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validado(Request $request): array
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cep' => ['nullable', 'string', 'regex:/^\d{5}-?\d{3}$/'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', Rule::in(array_keys(Cliente::ESTADOS))],
            'area_hectares' => ['nullable', 'numeric', 'min:0'],
            'localizacao' => ['nullable', 'string', 'max:255'],
            'observacao' => ['nullable', 'string', 'max:2000'],
        ]);

        // Mesma normalização de CEP usada em Cliente: sempre 00000-000,
        // aceitando os dois formatos (com ou sem traço) vindos do formulário.
        if (! empty($dados['cep'])) {
            $cepLimpo = preg_replace('/\D/', '', $dados['cep']);
            $dados['cep'] = substr($cepLimpo, 0, 5).'-'.substr($cepLimpo, 5);
        }

        return $dados;
    }
}
