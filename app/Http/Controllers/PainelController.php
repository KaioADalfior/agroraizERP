<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Procedimento;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PainelController extends Controller
{
    public function __invoke(): View
    {
        return view('painel', [
            'totalClientes' => Cliente::count(),
            'cadastrosPorMes' => $this->cadastrosPorMes(),
            'procedimentosPorTipo' => $this->procedimentosPorTipo(),
            'procedimentosPorFase' => $this->procedimentosPorFase(),
        ]);
    }

    /**
     * Quantidade de clientes cadastrados por mês, nos últimos 6 meses
     * (incluindo o atual). O agrupamento é feito em PHP (não em SQL) de
     * propósito: MySQL (produção) e SQLite (testes automatizados) não
     * compartilham a mesma função de formatação de data, e assim o gráfico
     * funciona igual nos dois.
     *
     * @return array<int, array{rotulo: string, total: int}>
     */
    private function cadastrosPorMes(): array
    {
        $inicio = now()->startOfMonth()->subMonths(5);

        $porMes = Cliente::query()
            ->where('created_at', '>=', $inicio)
            ->get(['created_at'])
            ->groupBy(fn ($cliente) => $cliente->created_at->format('Y-m'));

        $meses = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            $meses[] = [
                'rotulo' => Str::ucfirst($mes->translatedFormat('M/y')),
                'total' => $porMes->get($mes->format('Y-m'), collect())->count(),
            ];
        }

        return $meses;
    }

    /**
     * Quantidade de procedimentos por tipo de operação (Custeio/Investimento).
     * Sempre devolve as duas categorias, mesmo com zero procedimentos — dado
     * real, consultado na tabela `procedimentos`.
     *
     * @return array<int, array{rotulo: string, total: int}>
     */
    private function procedimentosPorTipo(): array
    {
        $contagem = Procedimento::query()
            ->selectRaw('tipo_operacao, count(*) as total')
            ->groupBy('tipo_operacao')
            ->pluck('total', 'tipo_operacao');

        return collect(Procedimento::TIPOS_OPERACAO)
            ->map(fn ($tipo) => ['rotulo' => $tipo, 'total' => (int) ($contagem[$tipo] ?? 0)])
            ->all();
    }

    /**
     * Quantidade de procedimentos por fase da proposta (campo livre, sem
     * lista fixa), limitada às 6 fases mais usadas. Dado real.
     *
     * @return array<int, array{rotulo: string, total: int}>
     */
    private function procedimentosPorFase(): array
    {
        return Procedimento::query()
            ->whereNotNull('fase_proposta')
            ->where('fase_proposta', '!=', '')
            ->selectRaw('fase_proposta, count(*) as total')
            ->groupBy('fase_proposta')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn ($linha) => ['rotulo' => $linha->fase_proposta, 'total' => (int) $linha->total])
            ->all();
    }
}
