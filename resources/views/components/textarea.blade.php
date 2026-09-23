{{--
    Campo de texto multilinha, no mesmo estilo do x-input.
    Uso: <x-textarea name="observacao" label="Observação" :value="$texto" rows="4" />
--}}
@props(['name', 'label' => null, 'value' => null, 'rows' => 4, 'hint' => null])

@php
    $id = $attributes->get('id', $name);
    $temErro = $errors->has($name);

    $classes = [
        'block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-raiz-900 shadow-xs transition',
        'placeholder:text-raiz-400 focus:outline-none focus:ring-2',
        $temErro ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20' : 'border-raiz-300 focus:border-raiz-600 focus:ring-raiz-600/20',
    ];
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-raiz-800">{{ $label }}</label>
    @endif

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($temErro) aria-invalid="true" @endif
        {{ $attributes->except(['id', 'rows'])->class($classes) }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="mt-1.5 flex items-center gap-1.5 text-sm text-red-600">
            <x-icon name="alert" class="size-4 shrink-0" />
            {{ $message }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-sm text-raiz-500">{{ $hint }}</p>
    @enderror
</div>
