@php
    $dentroDoModal = request()->header('Sec-Fetch-Dest') === 'iframe';
    $layoutCliente = $dentroDoModal ? 'layouts.embed' : 'layouts.app';
    $abaInicial = request()->query('aba', 'cliente');
@endphp
<x-dynamic-component :component="$layoutCliente" :title="$cliente->nome">
    <div x-data="{ aba: @js($abaInicial), excluindo: false }">
        {{-- Cabeçalho da ficha: nome do cliente + barra de ações --}}
        <div class="{{ $dentroDoModal ? 'mb-3 pb-3' : 'mb-6 pb-5' }} flex flex-col gap-3 border-b border-raiz-200 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-raiz-500">Cliente</p>
                <h1 class="font-display {{ $dentroDoModal ? 'text-base' : 'text-xl' }} tracking-wide text-raiz-800">{{ $cliente->nome }}</h1>
            </div>

            <div class="flex flex-wrap items-center gap-1.5">
                <x-button href="{{ route('clientes.create') }}" variant="secondary" :compact="$dentroDoModal">Novo</x-button>
                <x-button type="submit" form="form-cliente" :compact="$dentroDoModal">Salvar</x-button>
                <x-button href="{{ route('clientes.index') }}" variant="ghost" :compact="$dentroDoModal">Cancelar</x-button>
                <button
                    type="button"
                    x-on:click="excluindo = true"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-red-600 {{ $dentroDoModal ? 'px-2.5 py-1.5' : 'px-4 py-2.5' }} text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2"
                >
                    Deletar
                </button>
                <x-button href="{{ route('clientes.index') }}" variant="ghost" :compact="$dentroDoModal">Listar</x-button>
            </div>
        </div>

        {{-- Confirmação de exclusão --}}
        <div
            x-show="excluindo"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-raiz-950/60 p-4"
            x-on:keydown.escape.window="excluindo = false"
        >
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-lg" x-on:click.outside="excluindo = false">
                <h2 class="text-base font-semibold text-raiz-900">Excluir cliente?</h2>
                <p class="mt-2 text-sm text-raiz-600">
                    Tem certeza que deseja excluir <strong>{{ $cliente->nome }}</strong>? Essa ação não pode ser desfeita pela tela.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <x-button type="button" variant="ghost" x-on:click="excluindo = false">Cancelar</x-button>
                    <form method="POST" action="{{ route('clientes.destroy', $cliente) }}">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="danger">Excluir</x-button>
                    </form>
                </div>
            </div>
        </div>

        @if (session('sucesso'))
            <div class="mb-4 flex items-center gap-2 rounded-lg border border-raiz-200 bg-raiz-50 px-4 py-2.5 text-sm text-raiz-800">
                <x-icon name="check" class="size-5 shrink-0 text-raiz-600" />
                {{ session('sucesso') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-700">
                <x-icon name="alert" class="size-5 shrink-0" />
                <div>
                    <p class="font-semibold">Verifique os campos destacados abaixo.</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Abas --}}
        <div class="{{ $dentroDoModal ? 'mb-3' : 'mb-6' }} flex gap-1 border-b border-raiz-200">
            <button
                type="button"
                x-on:click="aba = 'cliente'"
                x-bind:class="aba === 'cliente' ? 'border-raiz-700 text-raiz-900' : 'border-transparent text-raiz-500 hover:text-raiz-800'"
                class="border-b-2 {{ $dentroDoModal ? 'px-3 py-1.5' : 'px-4 py-2.5' }} text-sm font-semibold transition"
            >
                Cliente
            </button>
            <button
                type="button"
                x-on:click="aba = 'propriedades'"
                x-bind:class="aba === 'propriedades' ? 'border-raiz-700 text-raiz-900' : 'border-transparent text-raiz-500 hover:text-raiz-800'"
                class="border-b-2 {{ $dentroDoModal ? 'px-3 py-1.5' : 'px-4 py-2.5' }} text-sm font-semibold transition"
            >
                Propriedades
            </button>
            <button
                type="button"
                x-on:click="aba = 'analises'"
                x-bind:class="aba === 'analises' ? 'border-raiz-700 text-raiz-900' : 'border-transparent text-raiz-500 hover:text-raiz-800'"
                class="border-b-2 {{ $dentroDoModal ? 'px-3 py-1.5' : 'px-4 py-2.5' }} text-sm font-semibold transition"
            >
                Análises de Solo
            </button>
            <button
                type="button"
                x-on:click="aba = 'procedimentos'"
                x-bind:class="aba === 'procedimentos' ? 'border-raiz-700 text-raiz-900' : 'border-transparent text-raiz-500 hover:text-raiz-800'"
                class="border-b-2 {{ $dentroDoModal ? 'px-3 py-1.5' : 'px-4 py-2.5' }} text-sm font-semibold transition"
            >
                Procedimentos
            </button>
        </div>

        {{-- Aba: Cliente — os campos já abrem prontos para edição --}}
        <div x-show="aba === 'cliente'">
            <x-card class="{{ $dentroDoModal ? 'p-4' : 'p-6 sm:p-8' }}">
                <form id="form-cliente" method="POST" action="{{ route('clientes.update', $cliente) }}">
                    @method('PUT')
                    @include('clientes._form')
                </form>
            </x-card>
        </div>

        {{-- Aba: Propriedades --}}
        <div x-show="aba === 'propriedades'" x-cloak>
            @include('clientes._propriedades', ['dentroDoModal' => $dentroDoModal])
        </div>

        {{-- Aba: Análises de Solo --}}
        <div x-show="aba === 'analises'" x-cloak>
            @include('clientes._analises', ['dentroDoModal' => $dentroDoModal])
        </div>

        {{-- Aba: Procedimentos --}}
        <div x-show="aba === 'procedimentos'" x-cloak>
            @include('clientes._procedimentos', ['dentroDoModal' => $dentroDoModal])
        </div>
    </div>
</x-dynamic-component>
