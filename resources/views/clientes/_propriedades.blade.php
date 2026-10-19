@php
    $dentroDoModal ??= false;
@endphp
<div class="grid {{ $dentroDoModal ? 'gap-4' : 'gap-6' }} lg:grid-cols-3">
    {{-- Formulário: nova propriedade --}}
    <x-card class="{{ $dentroDoModal ? 'p-4' : 'p-6' }} lg:col-span-1">
        <h2 class="text-sm font-semibold text-raiz-900">Nova propriedade</h2>
        <p class="mt-1 text-sm text-raiz-600">Cadastre uma fazenda, sítio ou área deste cliente.</p>

        <form method="POST" action="{{ route('propriedades.store', $cliente) }}" class="{{ $dentroDoModal ? 'mt-3 space-y-3' : 'mt-5 space-y-5' }}">
            @csrf
            <x-input name="nome" label="Nome da propriedade" placeholder="Ex.: Fazenda Boa Vista" required :compact="$dentroDoModal" />
            <x-input name="area_hectares" type="number" step="0.01" min="0" label="Área (ha)" placeholder="Ex.: 45.5" :compact="$dentroDoModal" />
            <x-input name="localizacao" label="Localização" placeholder="Ex.: Zona rural, km 12" :compact="$dentroDoModal" />
            <x-textarea name="observacao" label="Observação" rows="3" placeholder="Anotações sobre a propriedade" />

            <x-button type="submit" class="w-full" :compact="$dentroDoModal">Cadastrar propriedade</x-button>
        </form>
    </x-card>

    {{-- Lista de propriedades --}}
    <div class="lg:col-span-2">
        @if ($cliente->propriedades->isEmpty())
            <x-card class="{{ $dentroDoModal ? 'p-6' : 'p-10' }} text-center">
                <p class="text-sm text-raiz-600">Nenhuma propriedade cadastrada ainda para este cliente.</p>
            </x-card>
        @else
            <div class="{{ $dentroDoModal ? 'space-y-3' : 'space-y-4' }}" x-data="{ excluindoId: null }">
                @foreach ($cliente->propriedades as $propriedade)
                    <x-card class="{{ $dentroDoModal ? 'p-3' : 'p-5' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h3 class="truncate text-sm font-semibold text-raiz-900">{{ $propriedade->nome }}</h3>
                                <p class="mt-1 text-sm text-raiz-600">
                                    @if ($propriedade->area_hectares)
                                        {{ number_format((float) $propriedade->area_hectares, 2, ',', '.') }} ha
                                    @endif
                                    @if ($propriedade->area_hectares && $propriedade->localizacao)
                                        ·
                                    @endif
                                    {{ $propriedade->localizacao }}
                                </p>
                                @if ($propriedade->observacao)
                                    <p class="mt-2 text-sm text-raiz-500">{{ $propriedade->observacao }}</p>
                                @endif
                            </div>

                            <button
                                type="button"
                                x-on:click="excluindoId = {{ $propriedade->id }}"
                                class="shrink-0 rounded-lg px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                            >
                                Excluir
                            </button>
                        </div>
                    </x-card>

                    <template x-if="excluindoId === {{ $propriedade->id }}">
                        <div
                            x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center bg-raiz-950/60 p-4"
                            x-on:keydown.escape.window="excluindoId = null"
                        >
                            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-lg" x-on:click.outside="excluindoId = null">
                                <h2 class="text-base font-semibold text-raiz-900">Excluir propriedade?</h2>
                                <p class="mt-2 text-sm text-raiz-600">
                                    Tem certeza que deseja excluir <strong>{{ $propriedade->nome }}</strong>? Essa ação não pode ser desfeita pela tela.
                                </p>
                                <div class="mt-6 flex justify-end gap-3">
                                    <x-button type="button" variant="ghost" x-on:click="excluindoId = null">Cancelar</x-button>
                                    <form method="POST" action="{{ route('propriedades.destroy', $propriedade) }}">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" variant="danger">Excluir</x-button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </template>
                @endforeach
            </div>
        @endif
    </div>
</div>
