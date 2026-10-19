@php
    $nomeCompleto = trim((string) (auth()->user()->name ?? ''));
    $primeiroNome = $nomeCompleto !== '' ? \Illuminate\Support\Str::before($nomeCompleto, ' ') : null;

    $totalProcedimentos = collect($procedimentosPorTipo)->sum('total');
    $custeio = collect($procedimentosPorTipo)->firstWhere('rotulo', 'Custeio') ?? ['total' => 0];
    $investimento = collect($procedimentosPorTipo)->firstWhere('rotulo', 'Investimento') ?? ['total' => 0];
    $pctCusteio = $totalProcedimentos > 0 ? (int) round($custeio['total'] / $totalProcedimentos * 100) : 0;
    $pctInvestimento = $totalProcedimentos > 0 ? 100 - $pctCusteio : 0;

    // Acessos rápidos: só módulos que realmente existem no sistema. "Clientes"
    // abre o mesmo modal do menu lateral (reaproveita o x-data do body, em
    // layouts/app.blade.php); os demais usam rota normal ou ficam marcados
    // "Em breve", igual ao menu lateral.
    $acessosRapidos = [
        ['icone' => 'users', 'titulo' => 'Clientes', 'modal' => true],
        ['icone' => 'circle-stack', 'titulo' => 'Banco de dados', 'href' => route('culturas.index')],
        ['icone' => 'beaker', 'titulo' => 'Análises de solo', 'soon' => true],
        ['icone' => 'chart-bar', 'titulo' => 'Gráficos', 'soon' => true],
    ];
@endphp

<x-layouts.app title="Painel">
    <div class="space-y-5">
        {{-- Saudação + relógio de Brasília (em tempo real) e indicador de clientes --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-stretch">
            <div
                class="w-full rounded-xl border border-raiz-200 bg-white p-4 shadow-xs sm:max-w-xs"
                x-data="{
                    agora: new Date(),
                    nome: @js($primeiroNome),
                    intervalo: null,
                    init() {
                        this.intervalo = setInterval(() => { this.agora = new Date(); }, 1000);
                    },
                    destroy() {
                        clearInterval(this.intervalo);
                    },
                    partesBrasilia(opcoes) {
                        return new Intl.DateTimeFormat('en-US', Object.assign({ timeZone: 'America/Sao_Paulo' }, opcoes)).formatToParts(this.agora);
                    },
                    numeroParte(partes, tipo) {
                        const parte = partes.find(p => p.type === tipo);
                        return parte ? parseInt(parte.value, 10) : 0;
                    },
                    minutosDoDiaEmBrasilia() {
                        const partes = this.partesBrasilia({ hour: 'numeric', minute: 'numeric', hour12: false });
                        const hora = this.numeroParte(partes, 'hour') % 24;
                        const minuto = this.numeroParte(partes, 'minute');
                        return (hora * 60) + minuto;
                    },
                    saudacao() {
                        const minutos = this.minutosDoDiaEmBrasilia();
                        if (minutos >= 240 && minutos <= 720) { return 'Bom dia'; }
                        if (minutos >= 721 && minutos <= 1080) { return 'Boa tarde'; }
                        return 'Boa noite';
                    },
                    textoSaudacao() {
                        return this.nome ? (this.saudacao() + ', ' + this.nome + '!') : (this.saudacao() + '!');
                    },
                    relogioBrasilia() {
                        return new Intl.DateTimeFormat('pt-BR', { timeZone: 'America/Sao_Paulo', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).format(this.agora);
                    },
                    dataBrasilia() {
                        return new Intl.DateTimeFormat('pt-BR', { timeZone: 'America/Sao_Paulo', weekday: 'long', day: '2-digit', month: 'long' }).format(this.agora);
                    },
                }"
                x-init="init()"
            >
                <p class="text-[11px] font-semibold uppercase tracking-wide text-raiz-500" x-text="dataBrasilia().toUpperCase()"></p>
                <p class="mt-1 font-display text-base leading-snug text-raiz-900" x-text="textoSaudacao()"></p>
                <div class="mt-2 flex items-center gap-2 text-raiz-600">
                    <x-icon name="clock" class="size-4 shrink-0" />
                    <span class="text-sm font-medium tabular-nums" x-text="relogioBrasilia()"></span>
                    <span class="ml-auto shrink-0 rounded-full bg-raiz-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-raiz-600">Brasília</span>
                </div>
            </div>

            <div class="flex items-center gap-3 rounded-xl border border-raiz-200 bg-white p-4 shadow-xs">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-raiz-100 text-raiz-700">
                    <x-icon name="users" class="size-5" />
                </span>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-raiz-500">Clientes cadastrados</p>
                    <p class="font-display text-xl text-raiz-900">{{ $totalClientes }}</p>
                </div>
            </div>
        </div>

        {{-- Gráficos (dados reais, consultados no banco) --}}
        <section aria-labelledby="titulo-analitico">
            <h2 id="titulo-analitico" class="text-xs font-semibold uppercase tracking-[0.14em] text-raiz-500">Painel analítico</h2>

            <div class="mt-2 grid gap-3 lg:grid-cols-3">
                <x-card class="p-4">
                    <p class="text-sm font-semibold text-raiz-900">Evolução de cadastros</p>
                    <p class="mb-3 text-xs text-raiz-500">Clientes cadastrados nos últimos 6 meses</p>
                    <x-grafico-barras :dados="$cadastrosPorMes" />
                </x-card>

                <x-card class="p-4">
                    <p class="text-sm font-semibold text-raiz-900">Procedimentos por tipo</p>
                    <p class="mb-3 text-xs text-raiz-500">Custeio x Investimento</p>

                    @if ($totalProcedimentos > 0)
                        <div class="flex h-3 w-full overflow-hidden rounded-full bg-raiz-100">
                            <div class="h-full bg-raiz-700" style="width: {{ $pctCusteio }}%"></div>
                            <div class="h-full bg-raiz-300" style="width: {{ $pctInvestimento }}%"></div>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-xs text-raiz-600">
                            <span class="flex items-center gap-1.5">
                                <span class="size-2 shrink-0 rounded-full bg-raiz-700"></span>
                                Custeio ({{ $custeio['total'] }})
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="size-2 shrink-0 rounded-full bg-raiz-300"></span>
                                Investimento ({{ $investimento['total'] }})
                            </span>
                        </div>
                    @else
                        <div class="flex h-24 items-center justify-center text-center text-xs text-raiz-400">
                            Nenhum procedimento cadastrado ainda.
                        </div>
                    @endif
                </x-card>

                <x-card class="p-4">
                    <p class="text-sm font-semibold text-raiz-900">Fases das propostas</p>
                    <p class="mb-3 text-xs text-raiz-500">Procedimentos por fase cadastrada</p>
                    <x-grafico-barras :dados="$procedimentosPorFase" :horizontal="true" vazio="Nenhum procedimento com fase registrada ainda." />
                </x-card>
            </div>
        </section>

        {{-- Acessos rápidos --}}
        <section aria-labelledby="titulo-acessos">
            <h2 id="titulo-acessos" class="text-xs font-semibold uppercase tracking-[0.14em] text-raiz-500">Acessos rápidos</h2>

            <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($acessosRapidos as $acesso)
                    @if ($acesso['soon'] ?? false)
                        <span
                            class="flex cursor-not-allowed items-center gap-2 rounded-lg border border-raiz-200 bg-raiz-50 px-3 py-2.5 text-sm text-raiz-400"
                            title="Disponível em breve"
                            aria-disabled="true"
                        >
                            <x-icon :name="$acesso['icone']" class="size-4 shrink-0" />
                            <span class="flex-1 truncate">{{ $acesso['titulo'] }}</span>
                            <span class="shrink-0 rounded-full bg-raiz-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-raiz-500">Em breve</span>
                        </span>
                    @elseif ($acesso['modal'] ?? false)
                        <button
                            type="button"
                            x-on:click="modalClientesAberto = true; modalClientesTelaCheia = false; modalClientesCarregando = true"
                            class="flex items-center gap-2 rounded-lg border border-raiz-200 bg-white px-3 py-2.5 text-sm font-medium text-raiz-800 shadow-xs transition hover:border-raiz-300 hover:bg-raiz-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-raiz-500"
                        >
                            <x-icon :name="$acesso['icone']" class="size-4 shrink-0 text-raiz-600" />
                            <span class="flex-1 truncate text-left">{{ $acesso['titulo'] }}</span>
                        </button>
                    @else
                        <a
                            href="{{ $acesso['href'] }}"
                            class="flex items-center gap-2 rounded-lg border border-raiz-200 bg-white px-3 py-2.5 text-sm font-medium text-raiz-800 shadow-xs transition hover:border-raiz-300 hover:bg-raiz-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-raiz-500"
                        >
                            <x-icon :name="$acesso['icone']" class="size-4 shrink-0 text-raiz-600" />
                            <span class="flex-1 truncate">{{ $acesso['titulo'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.app>
