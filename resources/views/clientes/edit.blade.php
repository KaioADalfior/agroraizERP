<x-layouts.app :title="'Editar '.$cliente->nome">
    <x-page-header :title="$cliente->nome" subtitle="Editar dados do cliente">
        <x-button href="{{ route('clientes.index') }}" variant="secondary">Voltar</x-button>
    </x-page-header>

    <x-card class="max-w-3xl p-6 sm:p-8">
        <form method="POST" action="{{ route('clientes.update', $cliente) }}">
            @method('PUT')
            @include('clientes._form')

            <div class="mt-8 flex items-center gap-3">
                <x-button type="submit">Salvar alterações</x-button>
                <x-button href="{{ route('clientes.index') }}" variant="ghost">Cancelar</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
