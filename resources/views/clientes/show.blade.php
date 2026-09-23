<x-layouts.app :title="$cliente->nome">
    <x-page-header :title="$cliente->nome" subtitle="Cliente, propriedades e análises de solo">
        <div class="flex items-center gap-3">
            <x-button href="{{ route('clientes.edit', $cliente) }}" variant="secondary">Editar cliente</x-button>
            <x-button href="{{ route('clientes.index') }}" variant="ghost">Voltar</x-button>
        </div>
    </x-page-header>

    @if (session('sucesso'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-raiz-200 bg-raiz-50 px-4 py-3 text-sm text-raiz-800">
            <x-icon name="check" class="size-5 shrink-0 text-raiz-600" />
            {{ session('sucesso') }}
        </div>
    @endif

    {{-- Resumo do cliente --}}
    <x-card class="mb-8 grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-raiz-500">Endereço</p>
            <p class="mt-1 text-sm text-raiz-800">
                @if ($cliente->endereco || $cliente->cidade)
                    {{ $cliente->endereco }}@if($cliente->numero), {{ $cliente->numero }}@endif<br>
                    {{ $cliente->bairro }}@if($cliente->bairro && $cliente->cidade), @endif{{ $cliente->cidade }}{{ $cliente->cidade && $cliente->estado ? '/' : '' }}{{ $cliente->estado }}
                @else
                    —
                @endif
            </p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-raiz-500">CEP</p>
            <p class="mt-1 text-sm text-raiz-800">{{ $cliente->cep ?: '—' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-raiz-500">Data de cadastro</p>
            <p class="mt-1 text-sm text-raiz-800">{{ optional($cliente->data)->format('d/m/Y') ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-raiz-500">Observação</p>
            <p class="mt-1 text-sm text-raiz-800">{{ $cliente->observacao ?: '—' }}</p>
        </div>
    </x-card>

    <div x-data="{ aba: 'propriedades' }">
        <div class="mb-6 flex gap-2 border-b border-raiz-200">
            <button
                type="button"
                x-on:click="aba = 'propriedades'"
                x-bind:class="aba === 'propriedades' ? 'border-raiz-700 text-raiz-900' : 'border-transparent text-raiz-500 hover:text-raiz-800'"
                class="border-b-2 px-4 py-2.5 text-sm font-semibold transition"
            >
                Propriedades
            </button>
            <button
                type="button"
                x-on:click="aba = 'analises'"
                x-bind:class="aba === 'analises' ? 'border-raiz-700 text-raiz-900' : 'border-transparent text-raiz-500 hover:text-raiz-800'"
                class="border-b-2 px-4 py-2.5 text-sm font-semibold transition"
            >
                Análises de Solo
            </button>
        </div>

        {{-- Aba: Propriedades --}}
        <div x-show="aba === 'propriedades'">
            @include('clientes._propriedades')
        </div>

        {{-- Aba: Análises de Solo --}}
        <div x-show="aba === 'analises'" x-cloak>
            @include('clientes._analises')
        </div>
    </div>
</x-layouts.app>
