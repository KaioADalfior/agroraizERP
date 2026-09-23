@if ($analises->isEmpty())
    <x-card class="p-10 text-center">
        <p class="text-sm text-raiz-600">Nenhuma análise de solo cadastrada ainda para este cliente.</p>
        <p class="mt-2 text-sm text-raiz-500">
            O cadastro completo de análises (com os cálculos de SB, CTC, V% e m%) faz parte do módulo
            "Análises de solo", ainda planejado.
        </p>
    </x-card>
@else
    <x-card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-raiz-200 bg-raiz-50 text-xs font-semibold uppercase tracking-wide text-raiz-600">
                    <tr>
                        <th class="px-6 py-3">Propriedade</th>
                        <th class="px-6 py-3">Cultura</th>
                        <th class="px-6 py-3">Profundidade</th>
                        <th class="px-6 py-3">Data</th>
                        <th class="px-6 py-3">pH</th>
                        <th class="px-6 py-3">CTC (T)</th>
                        <th class="px-6 py-3">V%</th>
                        <th class="px-6 py-3">m%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-raiz-100">
                    @foreach ($analises as $analise)
                        <tr class="hover:bg-raiz-50/60">
                            <td class="px-6 py-4 font-medium text-raiz-900">{{ $analise->propriedade->nome }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $analise->cultura->nome ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $analise->profundidade ?: '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ optional($analise->data_coleta)->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $analise->ph ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $analise->ctc ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $analise->v_percentual ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $analise->m_percentual ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
@endif
