<x-layouts.app title="Novo cliente">
    <x-page-header title="Novo cliente" subtitle="Cadastre um novo cliente ou proprietário">
        <x-button href="{{ route('clientes.index') }}" variant="secondary">Voltar</x-button>
    </x-page-header>

    <x-card class="max-w-3xl p-6 sm:p-8">
        <form method="POST" action="{{ route('clientes.store') }}">
            @include('clientes._form')

            <div class="mt-8 flex items-center gap-3">
                <x-button type="submit">Salvar cliente</x-button>
                <x-button href="{{ route('clientes.index') }}" variant="ghost">Cancelar</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
