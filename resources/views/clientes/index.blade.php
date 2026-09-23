<x-layouts.app title="Clientes">
    <x-page-header title="Clientes" subtitle="Cadastro de clientes e proprietários">
        <x-button href="{{ route('clientes.create') }}">Novo cliente</x-button>
    </x-page-header>

    @if (session('sucesso'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-raiz-200 bg-raiz-50 px-4 py-3 text-sm text-raiz-800">
            <x-icon name="check" class="size-5 shrink-0 text-raiz-600" />
            {{ session('sucesso') }}
        </div>
    @endif

    <form method="GET" action="{{ route('clientes.index') }}" class="mb-6 max-w-md">
        <x-input name="busca" type="search" icon="search" placeholder="Buscar por nome, cidade ou bairro" :value="$busca" />
    </form>

    <x-card class="overflow-hidden" x-data="{ excluindoId: null }">
        @if ($clientes->isEmpty())
            <div class="p-10 text-center">
                <p class="text-sm text-raiz-600">
                    @if ($busca !== '')
                        Nenhum cliente encontrado para "{{ $busca }}".
                    @else
                        Nenhum cliente cadastrado ainda.
                    @endif
                </p>
                @if ($busca === '')
                    <x-button href="{{ route('clientes.create') }}" class="mt-4">Cadastrar o primeiro cliente</x-button>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-raiz-200 bg-raiz-50 text-xs font-semibold uppercase tracking-wide text-raiz-600">
                        <tr>
                            <th class="px-6 py-3">Cliente/Proprietário</th>
                            <th class="px-6 py-3">Cidade/UF</th>
                            <th class="px-6 py-3">Data</th>
                            <th class="px-6 py-3">Observação</th>
                            <th class="px-6 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-raiz-100">
                        @foreach ($clientes as $cliente)
                            <tr class="hover:bg-raiz-50/60">
                                <td class="px-6 py-4 font-medium text-raiz-900">{{ $cliente->nome }}</td>
                                <td class="px-6 py-4 text-raiz-700">
                                    @if ($cliente->cidade || $cliente->estado)
                                        {{ $cliente->cidade }}{{ $cliente->cidade && $cliente->estado ? '/' : '' }}{{ $cliente->estado }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-raiz-700">{{ optional($cliente->data)->format('d/m/Y') ?? '—' }}</td>
                                <td class="max-w-xs truncate px-6 py-4 text-raiz-700">{{ $cliente->observacao ?: '—' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-button href="{{ route('clientes.show', $cliente) }}" variant="ghost">Ver</x-button>
                                        <x-button href="{{ route('clientes.edit', $cliente) }}" variant="ghost">Editar</x-button>
                                        <button
                                            type="button"
                                            x-on:click="excluindoId = {{ $cliente->id }}"
                                            class="rounded-lg px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                                        >
                                            Excluir
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <template x-if="excluindoId === {{ $cliente->id }}">
                                <div
                                    x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-raiz-950/60 p-4"
                                    x-on:keydown.escape.window="excluindoId = null"
                                >
                                    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-lg" x-on:click.outside="excluindoId = null">
                                        <h2 class="text-base font-semibold text-raiz-900">Excluir cliente?</h2>
                                        <p class="mt-2 text-sm text-raiz-600">
                                            Tem certeza que deseja excluir <strong>{{ $cliente->nome }}</strong>? Essa ação não pode ser desfeita pela tela.
                                        </p>
                                        <div class="mt-6 flex justify-end gap-3">
                                            <x-button type="button" variant="ghost" x-on:click="excluindoId = null">Cancelar</x-button>
                                            <form method="POST" action="{{ route('clientes.destroy', $cliente) }}">
                                                @csrf
                                                @method('DELETE')
                                                <x-button type="submit" variant="danger">Excluir</x-button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-raiz-200 px-6 py-4">
                {{ $clientes->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.app>
