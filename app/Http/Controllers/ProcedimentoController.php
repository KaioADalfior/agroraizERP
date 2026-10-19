<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Procedimento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class ProcedimentoController extends Controller
{
    public function store(Request $request, Cliente $cliente): RedirectResponse|JsonResponse
    {
        $procedimento = $cliente->procedimentos()->create($this->validado($request));

        if ($request->wantsJson()) {
            return response()->json([
                'sucesso' => 'Procedimento cadastrado com sucesso.',
                'procedimento' => $procedimento,
            ], 201);
        }

        return redirect()->route('clientes.edit', ['cliente' => $cliente, 'aba' => 'procedimentos'])->with('sucesso', 'Procedimento cadastrado com sucesso.');
    }

    public function update(Request $request, Procedimento $procedimento): RedirectResponse|JsonResponse
    {
        $procedimento->update($this->validado($request));

        if ($request->wantsJson()) {
            return response()->json([
                'sucesso' => 'Procedimento atualizado com sucesso.',
                'procedimento' => $procedimento->fresh(),
            ]);
        }

        return redirect()->route('clientes.edit', ['cliente' => $procedimento->cliente_id, 'aba' => 'procedimentos'])->with('sucesso', 'Procedimento atualizado com sucesso.');
    }

    public function destroy(Request $request, Procedimento $procedimento): RedirectResponse|JsonResponse
    {
        $clienteId = $procedimento->cliente_id;

        $procedimento->delete();

        if ($request->wantsJson()) {
            return response()->json(['sucesso' => 'Procedimento removido.']);
        }

        return redirect()->route('clientes.edit', ['cliente' => $clienteId, 'aba' => 'procedimentos'])->with('sucesso', 'Procedimento removido.');
    }

    /**
     * Lista de bancos brasileiros (código + nome) para o campo "Banco" do procedimento.
     *
     * Fonte: BrasilAPI — https://brasilapi.com.br/api/banks/v1 — uma API pública,
     * comunitária e sem autenticação que espelha a lista de participantes do SPB
     * divulgada pelo Banco Central. A busca é feita aqui no servidor (nunca
     * diretamente do navegador do usuário) e o resultado fica em cache por 24h,
     * para não depender da disponibilidade da BrasilAPI a cada vez que alguém abre
     * o formulário. Enquanto o cache estiver válido esta rota não faz nenhuma
     * chamada externa.
     *
     * Se a busca falhar (API fora do ar, instável ou mudou de formato) e não houver
     * nenhum cache, a resposta vem com `bancos: []` e `erro: true` — o front-end
     * (ver resources/views/clientes/_procedimentos.blade.php) usa isso para liberar
     * um campo de texto livre no lugar da busca, sem bloquear o resto do formulário.
     * Se já existir uma lista obtida com sucesso anteriormente (cache "de reserva",
     * sem expiração), ela é devolvida normalmente em vez do erro, mesmo que o cache
     * principal de 24h já tenha vencido.
     */
    public function bancos(): JsonResponse
    {
        $chaveCache = 'bancos_brasileiros';
        $chaveReserva = 'bancos_brasileiros_reserva';

        try {
            $bancos = Cache::remember($chaveCache, now()->addDay(), function () use ($chaveReserva) {
                $resposta = Http::timeout(5)->get('https://brasilapi.com.br/api/banks/v1');

                if (! $resposta->successful()) {
                    throw new \RuntimeException('BrasilAPI respondeu com status '.$resposta->status().'.');
                }

                $dados = $resposta->json();

                if (! is_array($dados)) {
                    throw new \RuntimeException('Resposta da BrasilAPI em formato inesperado.');
                }

                $lista = collect($dados)
                    ->filter(fn ($banco) => is_array($banco) && ! empty($banco['name']) && isset($banco['code']) && is_numeric($banco['code']))
                    ->map(fn ($banco) => [
                        'codigo' => (int) $banco['code'],
                        'nome' => $banco['fullName'] ?? $banco['name'],
                    ])
                    ->unique('codigo')
                    ->sortBy('nome')
                    ->values()
                    ->all();

                if (empty($lista)) {
                    throw new \RuntimeException('BrasilAPI devolveu uma lista vazia.');
                }

                // Cópia "de reserva", sem expiração automática: só é lida se uma
                // atualização futura falhar depois que o cache principal vencer.
                Cache::forever($chaveReserva, $lista);

                return $lista;
            });

            return response()->json(['bancos' => $bancos]);
        } catch (Throwable $erro) {
            Log::warning('Não foi possível obter a lista de bancos da BrasilAPI.', ['erro' => $erro->getMessage()]);

            return response()->json([
                'bancos' => Cache::get($chaveReserva, []),
                'erro' => true,
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validado(Request $request): array
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'banco_nome' => ['nullable', 'string', 'max:255'],
            'banco_codigo' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'tipo_operacao' => ['required', 'string', Rule::in(Procedimento::TIPOS_OPERACAO)],
            'fase_proposta' => ['nullable', 'string', 'max:255'],
            'porcentagem_cobrada' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pagamento_recebido' => ['nullable', 'boolean'],
        ]);

        // Checkbox/toggle ausente no payload == "não marcado"; nunca inferido a partir
        // de outro campo, só do valor explicitamente enviado pelo front-end.
        $dados['pagamento_recebido'] = $request->boolean('pagamento_recebido');

        return $dados;
    }
}
