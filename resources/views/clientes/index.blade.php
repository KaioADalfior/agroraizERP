@php
    // Carregada dentro do iframe do modal de Clientes (ver layouts.app)? Usa o
    // layout leve (sem menu lateral duplicado) e uma densidade visual mais
    // compacta, para reproduzir as proporções da referência.
    $dentroDoModal = request()->header('Sec-Fetch-Dest') === 'iframe';
    $layoutCliente = $dentroDoModal ? 'layouts.embed' : 'layouts.app';
@endphp
<x-dynamic-component :component="$layoutCliente" title="Clientes">
    <x-page-header title="Clientes" subtitle="Clique em um cliente para abrir a ficha" :compact="$dentroDoModal" />

    @if (session('sucesso'))
        <div class="mb-4 flex items-center gap-2 rounded-lg border border-raiz-200 bg-raiz-50 px-4 py-2.5 text-sm text-raiz-800">
            <x-icon name="check" class="size-5 shrink-0 text-raiz-600" />
            {{ session('sucesso') }}
        </div>
    @endif

    <form method="GET" action="{{ route('clientes.index') }}" class="{{ $dentroDoModal ? 'mb-3' : 'mb-6' }} max-w-md">
        <x-input name="busca" type="search" icon="search" placeholder="Buscar por nome, cidade ou bairro" :value="$busca" :compact="$dentroDoModal" />
    </form>

    <x-card class="overflow-hidden">
        @if ($clientes->isEmpty())
            <div class="{{ $dentroDoModal ? 'p-6' : 'p-10' }} text-center">
                <p class="text-sm text-raiz-600">
                    @if ($busca !== '')
                        Nenhum cliente encontrado para "{{ $busca }}".
                    @else
                        Nenhum cliente cadastrado ainda. Use o botão "Novo" dentro da ficha para cadastrar o primeiro.
                    @endif
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-raiz-200 bg-raiz-50 text-xs font-semibold uppercase tracking-wide text-raiz-600">
                        <tr>
                            <th class="{{ $dentroDoModal ? 'px-4 py-2' : 'px-6 py-3' }}">Cliente/Proprietário</th>
                            <th class="{{ $dentroDoModal ? 'px-4 py-2' : 'px-6 py-3' }}">Cidade/UF</th>
                            <th class="{{ $dentroDoModal ? 'px-4 py-2' : 'px-6 py-3' }}">Data</th>
                            <th class="{{ $dentroDoModal ? 'px-4 py-2' : 'px-6 py-3' }}">Observação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-raiz-100">
                        @foreach ($clientes as $cliente)
                            <tr
                                class="cursor-pointer select-none hover:bg-raiz-50/60"
                                title="Clique para abrir"
                                x-on:click="window.location = '{{ route('clientes.edit', $cliente) }}'"
                            >
                                <td class="{{ $dentroDoModal ? 'px-4 py-2' : 'px-6 py-4' }} font-medium text-raiz-900">{{ $cliente->nome }}</td>
                                <td class="{{ $dentroDoModal ? 'px-4 py-2' : 'px-6 py-4' }} text-raiz-700">
                                    @if ($cliente->cidade || $cliente->estado)
                                        {{ $cliente->cidade }}{{ $cliente->cidade && $cliente->estado ? '/' : '' }}{{ $cliente->estado }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="{{ $dentroDoModal ? 'px-4 py-2' : 'px-6 py-4' }} text-raiz-700">{{ optional($cliente->data)->format('d/m/Y') ?? '—' }}</td>
                                <td class="{{ $dentroDoModal ? 'px-4 py-2' : 'px-6 py-4' }} max-w-xs truncate text-raiz-700">{{ $cliente->observacao ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-raiz-200 {{ $dentroDoModal ? 'px-4 py-2.5' : 'px-6 py-4' }}">
                {{ $clientes->links() }}
            </div>
        @endif
    </x-card>
</x-dynamic-component>
