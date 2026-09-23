{{--
    Cabeçalho padrão de cada página.
    Uso: <x-page-header title="Clientes" subtitle="Cadastro de clientes e proprietários"> <x-button>Novo</x-button> </x-page-header>
    O conteúdo (slot) aparece à direita, para botões de ação.
--}}
@props(['title', 'subtitle' => null])

<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="font-display text-xl tracking-wide text-raiz-800">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-2 text-sm text-raiz-600">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($slot->hasActualContent())
        <div class="flex shrink-0 items-center gap-3">{{ $slot }}</div>
    @endif
</div>
