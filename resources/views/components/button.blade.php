{{--
    Botão padrão. Variantes: primary (padrão), secondary, ghost.
    Uso: <x-button type="submit">Salvar</x-button>  ou  <x-button href="/algum-lugar" variant="secondary">Voltar</x-button>
--}}
@props(['variant' => 'primary', 'type' => 'button', 'href' => null])

@php
    $classes = [
        'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold transition',
        'focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60',
        match ($variant) {
            'secondary' => 'border border-raiz-300 bg-white text-raiz-800 hover:bg-raiz-50 focus-visible:ring-raiz-500',
            'ghost' => 'text-raiz-700 hover:bg-raiz-100 focus-visible:ring-raiz-500',
            default => 'bg-raiz-700 text-white shadow-sm hover:bg-raiz-800 focus-visible:ring-raiz-600',
        },
    ];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
