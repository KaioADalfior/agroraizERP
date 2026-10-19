@php
    $dentroDoModal ??= false;
    $cel = $dentroDoModal ? 'px-4 py-2' : 'px-6 py-4';
    $celCabecalho = $dentroDoModal ? 'px-4 py-2' : 'px-6 py-3';
@endphp
@if ($analises->isEmpty())
    <x-card class="{{ $dentroDoModal ? 'p-6' : 'p-10' }} text-center">
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
                        <th class="{{ $celCabecalho }}">Propriedade</th>
                        <th class="{{ $celCabecalho }}">Cultura</th>
                        <th class="{{ $celCabecalho }}">Profundidade</th>
                        <th class="{{ $celCabecalho }}">Data</th>
                        <th class="{{ $celCabecalho }}">pH</th>
                        <th class="{{ $celCabecalho }}">CTC (T)</th>
                        <th class="{{ $celCabecalho }}">V%</th>
                        <th class="{{ $celCabecalho }}">m%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-raiz-100">
                    @foreach ($analises as $analise)
                        <tr class="hover:bg-raiz-50/60">
                            <td class="{{ $cel }} font-medium text-raiz-900">{{ $analise->propriedade->nome }}</td>
                            <td class="{{ $cel }} text-raiz-700">{{ $analise->cultura->nome ?? '—' }}</td>
                            <td class="{{ $cel }} text-raiz-700">{{ $analise->profundidade ?: '—' }}</td>
                            <td class="{{ $cel }} text-raiz-700">{{ optional($analise->data_coleta)->format('d/m/Y') ?? '—' }}</td>
                            <td class="{{ $cel }} text-raiz-700">{{ $analise->ph ?? '—' }}</td>
                            <td class="{{ $cel }} text-raiz-700">{{ $analise->ctc ?? '—' }}</td>
                            <td class="{{ $cel }} text-raiz-700">{{ $analise->v_percentual ?? '—' }}</td>
                            <td class="{{ $cel }} text-raiz-700">{{ $analise->m_percentual ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
@endif
