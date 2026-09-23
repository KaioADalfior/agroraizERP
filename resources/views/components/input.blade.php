{{--
    Campo de formulário padrão.
    Uso: <x-input name="email" label="E-mail" type="email" icon="envelope" />
    Mostra sozinho o valor antigo (old) e a mensagem de erro do campo.
--}}
@props(['name', 'label' => null, 'type' => 'text', 'icon' => null, 'hint' => null])

@php
    $id = $attributes->get('id', $name);
    $temErro = $errors->has($name);
    $ehSenha = $type === 'password';

    $classes = [
        'block w-full rounded-lg border bg-white py-2.5 text-sm text-raiz-900 shadow-xs transition',
        'placeholder:text-raiz-400 focus:outline-none focus:ring-2',
        $icon ? 'pl-11' : 'pl-3.5',
        $ehSenha ? 'pr-11' : 'pr-3.5',
        $temErro ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20' : 'border-raiz-300 focus:border-raiz-600 focus:ring-raiz-600/20',
    ];
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-raiz-800">{{ $label }}</label>
    @endif

    <div class="relative" x-data="{ mostrar: false }">
        @if ($icon)
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-raiz-400">
                <x-icon :name="$icon" class="size-5" />
            </span>
        @endif

        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if ($ehSenha) x-bind:type="mostrar ? 'text' : 'password'" @else value="{{ old($name) }}" @endif
            @if ($temErro) aria-invalid="true" @endif
            {{ $attributes->except('id')->class($classes) }}
        >

        @if ($ehSenha)
            <button
                type="button"
                x-on:click="mostrar = ! mostrar"
                x-bind:aria-label="mostrar ? 'Ocultar senha' : 'Mostrar senha'"
                class="absolute inset-y-0 right-0 flex items-center rounded-r-lg px-3.5 text-raiz-400 transition hover:text-raiz-700 focus:outline-none focus-visible:text-raiz-700"
                tabindex="-1"
            >
                <span x-show="! mostrar"><x-icon name="eye" class="size-5" /></span>
                <span x-show="mostrar" x-cloak><x-icon name="eye-slash" class="size-5" /></span>
            </button>
        @endif
    </div>

    @error($name)
        <p class="mt-1.5 flex items-center gap-1.5 text-sm text-red-600">
            <x-icon name="alert" class="size-4 shrink-0" />
            {{ $message }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-sm text-raiz-500">{{ $hint }}</p>
    @enderror
</div>
