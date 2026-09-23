{{--
    Campo select padrão, no mesmo estilo do x-input.
    Uso: <x-select name="estado" label="Estado"><option value="SP">SP - São Paulo</option></x-select>
--}}
@props(['name', 'label' => null, 'hint' => null])

@php
    $id = $attributes->get('id', $name);
    $temErro = $errors->has($name);

    $classes = [
        'block w-full rounded-lg border bg-white py-2.5 pl-3.5 pr-10 text-sm text-raiz-900 shadow-xs transition',
        'focus:outline-none focus:ring-2',
        $temErro ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20' : 'border-raiz-300 focus:border-raiz-600 focus:ring-raiz-600/20',
    ];
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-raiz-800">{{ $label }}</label>
    @endif

    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @if ($temErro) aria-invalid="true" @endif
        {{ $attributes->except('id')->class($classes) }}
    >
        {{ $slot }}
    </select>

    @error($name)
        <p class="mt-1.5 flex items-center gap-1.5 text-sm text-red-600">
            <x-icon name="alert" class="size-4 shrink-0" />
            {{ $message }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-sm text-raiz-500">{{ $hint }}</p>
    @enderror
</div>
