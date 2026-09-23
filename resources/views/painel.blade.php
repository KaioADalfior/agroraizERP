@php
    $primeiroNome = \Illuminate\Support\Str::before(trim(auth()->user()->name), ' ');

    // Módulos previstos (espelham as abas da planilha "Interpretação de Análise de Solo").
    $modulos = [
        ['icone' => 'users', 'titulo' => 'Clientes', 'texto' => 'Cadastro de clientes e proprietários, com cidade/estado, data e observações.', 'href' => route('clientes.index')],
        ['icone' => 'beaker', 'titulo' => 'Análises de solo', 'texto' => 'Lançamento dos resultados por talhão e profundidade, com cálculos automáticos.', 'href' => null],
        ['icone' => 'chart-bar', 'titulo' => 'Gráficos', 'texto' => 'Interpretação visual dos nutrientes de cada amostra, pronta para apresentar.', 'href' => null],
        ['icone' => 'circle-stack', 'titulo' => 'Banco de dados', 'texto' => 'Teores adequados por cultura, usados como referência na interpretação.', 'href' => null],
    ];
@endphp

<x-layouts.app title="Painel">
    {{-- Boas-vindas --}}
    <section class="relative overflow-hidden rounded-2xl bg-raiz-700 px-6 py-10 text-white shadow-sm sm:px-10 sm:py-12">
        <div class="pointer-events-none absolute inset-0 bg-linear-to-br from-raiz-600/40 via-transparent to-raiz-900/50"></div>
        <x-curvas class="text-white/15" />

        <div class="relative max-w-2xl">
            <p class="text-sm font-medium text-white/70">Painel</p>
            <h1 class="mt-2 font-display text-2xl leading-snug tracking-wide sm:text-3xl">Olá, {{ $primeiroNome }}</h1>
            <p class="mt-4 text-base leading-relaxed text-white/80">
                Bem-vindo ao sistema de gestão da Agro Raiz. Aqui vão ficar os clientes, as análises de solo e os gráficos de interpretação.
            </p>
        </div>
    </section>

    {{-- Módulos --}}
    <section class="mt-10" aria-labelledby="titulo-modulos">
        <h2 id="titulo-modulos" class="text-sm font-semibold uppercase tracking-[0.14em] text-raiz-600">Módulos</h2>

        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($modulos as $modulo)
                <x-card class="flex flex-col p-6">
                    <div class="flex items-start justify-between">
                        <span class="flex size-11 items-center justify-center rounded-xl bg-raiz-100 text-raiz-700">
                            <x-icon :name="$modulo['icone']" class="size-6" />
                        </span>
                        @if ($modulo['href'])
                            <span class="inline-flex items-center gap-1 rounded-full bg-raiz-700/10 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-raiz-700">
                                <x-icon name="check" class="size-3.5" />
                                Ativo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-raiz-100 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-raiz-600">
                                <x-icon name="clock" class="size-3.5" />
                                Em breve
                            </span>
                        @endif
                    </div>

                    <h3 class="mt-5 text-base font-semibold text-raiz-900">{{ $modulo['titulo'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-raiz-600">{{ $modulo['texto'] }}</p>

                    @if ($modulo['href'])
                        <x-button href="{{ $modulo['href'] }}" variant="ghost" class="mt-4 self-start">Abrir</x-button>
                    @endif
                </x-card>
            @endforeach
        </div>
    </section>
</x-layouts.app>
