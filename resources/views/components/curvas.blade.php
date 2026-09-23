{{-- Linhas decorativas (lembram curvas de nível / linhas de plantio). Cor definida por text-* na classe. --}}
@props(['linhas' => 16])

<svg {{ $attributes->merge(['class' => 'pointer-events-none absolute inset-0 h-full w-full']) }} viewBox="0 0 800 800" preserveAspectRatio="xMidYMid slice" fill="none" aria-hidden="true">
    @for ($i = 0; $i < $linhas; $i++)
        <path
            d="M-60 {{ 180 + $i * 34 }} C 120 {{ 90 + $i * 34 }}, 260 {{ 330 + $i * 30 }}, 430 {{ 250 + $i * 34 }} S 700 {{ 120 + $i * 38 }}, 860 {{ 220 + $i * 34 }}"
            stroke="currentColor"
            stroke-width="1.25"
            opacity="{{ number_format(max(0.15, 0.9 - $i * 0.045), 2, '.', '') }}"
        />
    @endfor
</svg>
