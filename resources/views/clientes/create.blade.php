@php
    $dentroDoModal = request()->header('Sec-Fetch-Dest') === 'iframe';
    $layoutCliente = $dentroDoModal ? 'layouts.embed' : 'layouts.app';
@endphp
<x-dynamic-component :component="$layoutCliente" title="Novo cliente">
    <div class="{{ $dentroDoModal ? 'mb-3 pb-3' : 'mb-6 pb-5' }} flex flex-col gap-3 border-b border-raiz-200 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-raiz-500">Novo cliente</p>
            <h1 class="font-display {{ $dentroDoModal ? 'text-base' : 'text-xl' }} tracking-wide text-raiz-800">Cadastro de cliente</h1>
        </div>

        <div class="flex flex-wrap items-center gap-1.5">
            <x-button type="submit" form="form-cliente" :compact="$dentroDoModal">Salvar</x-button>
            <x-button href="{{ route('clientes.index') }}" variant="ghost" :compact="$dentroDoModal">Cancelar</x-button>
            <x-button href="{{ route('clientes.index') }}" variant="ghost" :compact="$dentroDoModal">Listar</x-button>
        </div>
    </div>

    <x-card class="{{ $dentroDoModal ? 'p-4' : 'p-6 sm:p-8' }}">
        <form id="form-cliente" method="POST" action="{{ route('clientes.store') }}">
            @include('clientes._form')
        </form>
    </x-card>
</x-dynamic-component>
