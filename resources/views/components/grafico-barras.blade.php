{{--
    Gráfico de barras compacto, em CSS puro (sem lib nova).
    Uso: <x-grafico-barras :dados="$lista" /> (vertical, para séries curtas tipo meses)
         <x-grafico-barras :dados="$lista" :horizontal="true" /> (barras horizontais, para listas com rótulos maiores)
    $dados é uma lista de ['rotulo' => string, 'total' => int].
--}}
@props(['dados', 'horizontal' => false, 'vazio' => 'Sem dados ainda.'])

@php
    $maximo = max(1, collect($dados)->max('total') ?? 0);
    $temAlgumValor = collect($dados)->sum('total') > 0;
@endphp

@if (count($dados) === 0 || ! $temAlgumValor)
    <div class="flex h-24 items-center justify-center text-center text-xs text-raiz-400">
        {{ $vazio }}
    </div>
@elseif ($horizontal)
    <div class="space-y-2">
        @foreach ($dados as $item)
            @php
                $percentual = $item['total'] > 0 ? max(4, (int) round($item['total'] / $maximo * 100)) : 0;
            @endphp
            <div>
                <div class="mb-1 flex items-center justify-between gap-2 text-xs text-raiz-600">
                    <span class="truncate">{{ $item['rotulo'] }}</span>
                    <span class="shrink-0 font-semibold text-raiz-800">{{ $item['total'] }}</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-raiz-100">
                    <div class="h-full rounded-full bg-raiz-700" style="width: {{ $percentual }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="flex h-28 items-end gap-2">
        @foreach ($dados as $item)
            @php
                $percentual = $item['total'] > 0 ? max(6, (int) round($item['total'] / $maximo * 100)) : 0;
            @endphp
            <div class="flex flex-1 flex-col items-center gap-1">
                <span class="text-[11px] font-semibold text-raiz-800">{{ $item['total'] }}</span>
                <div class="flex h-16 w-full items-end overflow-hidden rounded-t bg-raiz-100">
                    <div class="w-full rounded-t bg-raiz-700" style="height: {{ $percentual }}%"></div>
                </div>
                <span class="text-[10px] text-raiz-500">{{ $item['rotulo'] }}</span>
            </div>
        @endforeach
    </div>
@endif
