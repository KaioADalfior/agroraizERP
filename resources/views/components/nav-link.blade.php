{{--
    Item do menu lateral.
    Uso: <x-nav-link :href="route('painel')" icon="home" :active="request()->routeIs('painel')">Painel</x-nav-link>
    Para módulos ainda não criados, use o atributo :soon="true" (fica desabilitado com selo "Em breve").
    Para um item que abre algo em vez de navegar (ex.: um modal), use :as="'button'" e passe
    x-on:click normalmente: <x-nav-link :as="'button'" x-on:click="..." icon="users">Clientes</x-nav-link>
--}}
@props(['icon', 'href' => '#', 'active' => false, 'soon' => false, 'as' => 'a'])

@php
    $classes = [
        'flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60',
        'bg-white text-raiz-800 shadow-sm' => $active,
        'text-white/80 hover:bg-white/10 hover:text-white' => ! $active,
    ];
@endphp

@if ($soon)
    <span
        class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/45"
        title="Disponível em breve"
        aria-disabled="true"
    >
        <x-icon :name="$icon" class="size-5 shrink-0" />
        <span class="flex-1">{{ $slot }}</span>
        <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white/60">Em breve</span>
    </span>
@elseif ($as === 'button')
    <button
        type="button"
        @if ($active) aria-current="page" @endif
        {{ $attributes->class($classes) }}
    >
        <x-icon :name="$icon" class="size-5 shrink-0" />
        <span class="flex-1 text-left">{{ $slot }}</span>
    </button>
@else
    <a
        href="{{ $href }}"
        @if ($active) aria-current="page" @endif
        {{ $attributes->class($classes) }}
    >
        <x-icon :name="$icon" class="size-5 shrink-0" />
        <span class="flex-1">{{ $slot }}</span>
    </a>
@endif
