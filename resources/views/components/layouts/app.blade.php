{{--
    Layout da área logada: menu lateral verde + barra superior + conteúdo.
    Uso: <x-layouts.app title="Painel"> ...conteúdo... </x-layouts.app>
--}}
@props(['title' => null])

@php
    $usuario = auth()->user();
    $iniciais = \Illuminate\Support\Str::upper(
        collect(explode(' ', trim($usuario->name)))
            ->filter()
            ->map(fn ($parte) => \Illuminate\Support\Str::substr($parte, 0, 1))
            ->take(2)
            ->implode('')
    );
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        @include('partials.head', ['title' => $title])
    </head>
    <body
        class="min-h-full bg-raiz-50 font-sans text-raiz-900 antialiased"
        x-data="{ menuAberto: false, modalClientesAberto: false, modalClientesTelaCheia: false, modalClientesCarregando: false }"
        x-on:keydown.escape.window="menuAberto = false"
    >
        {{-- Fundo escuro atrás do menu no celular --}}
        <div
            x-show="menuAberto"
            x-cloak
            x-transition.opacity
            x-on:click="menuAberto = false"
            class="fixed inset-0 z-40 bg-raiz-950/60 lg:hidden"
        ></div>

        {{-- Menu lateral --}}
        <aside
            x-bind:class="menuAberto ? 'max-lg:translate-x-0' : ''"
            class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-raiz-700 text-white transition-transform duration-200 ease-out lg:translate-x-0"
            aria-label="Menu principal"
        >
            <div class="relative flex items-center justify-between px-6 pb-6 pt-7">
                <a href="{{ route('painel') }}" class="rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60" aria-label="Agro Raiz — ir para o painel">
                    <x-logo class="w-36" />
                </a>

                <button
                    type="button"
                    x-on:click="menuAberto = false"
                    class="rounded-lg p-2 text-white/70 transition hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60 lg:hidden"
                    aria-label="Fechar menu"
                >
                    <x-icon name="close" class="size-6" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 pb-4">
                <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-white/50">Menu principal</p>

                <div class="space-y-1">
                    <x-nav-link :href="route('painel')" icon="home" :active="request()->routeIs('painel')">Painel</x-nav-link>
                    <x-nav-link
                        :as="'button'"
                        icon="users"
                        x-bind:class="modalClientesAberto ? 'bg-white text-raiz-800 shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white'"
                        x-on:click="modalClientesAberto = true; modalClientesTelaCheia = false; modalClientesCarregando = true; menuAberto = false"
                    >
                        Clientes
                    </x-nav-link>
                    <x-nav-link icon="beaker" :soon="true">Análises de solo</x-nav-link>
                    <x-nav-link icon="chart-bar" :soon="true">Gráficos</x-nav-link>
                    <x-nav-link :href="route('culturas.index')" icon="circle-stack" :active="request()->routeIs('culturas.*')">Banco de dados</x-nav-link>
                </div>
            </nav>

            {{-- Usuário logado --}}
            <div class="border-t border-white/10 p-4">
                <div class="flex items-center gap-3 rounded-lg px-2 py-2">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-sm font-semibold text-white">{{ $iniciais }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold">{{ $usuario->name }}</p>
                        <p class="truncate text-xs text-white/60">{{ $usuario->email }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 transition hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        <x-icon name="logout" class="size-5" />
                        Sair
                    </button>
                </form>
            </div>
        </aside>

        {{-- Área principal --}}
        <div class="lg:pl-72">
            <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-raiz-200 bg-white/85 px-4 backdrop-blur sm:px-6 lg:px-8">
                <button
                    type="button"
                    x-on:click="menuAberto = true"
                    class="-ml-2 rounded-lg p-2 text-raiz-700 transition hover:bg-raiz-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-raiz-500 lg:hidden"
                    aria-label="Abrir menu"
                >
                    <x-icon name="menu" class="size-6" />
                </button>

                <span class="font-display text-sm tracking-wide text-raiz-800 lg:hidden">AGRO RAIZ</span>

                <p class="ml-auto hidden text-sm text-raiz-600 first-letter:uppercase sm:block">
                    {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
                </p>
            </header>

            <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>

            <footer class="mx-auto w-full max-w-7xl px-4 pb-8 text-xs text-raiz-500 sm:px-6 lg:px-8">
                © {{ date('Y') }} DAK Soluções Digitais - TODOS OS DIREITOS RESERVADOS
            </footer>
        </div>

        {{--
            Modal de Clientes: abre por cima de qualquer página do painel ao
            clicar em "Clientes" no menu, carregando a listagem (e, a partir
            dela, a ficha de cada cliente) num iframe — reaproveitando as
            mesmas páginas/rotas/controllers, sem duplicar nada. Fecha
            exclusivamente pelo botão X (de propósito: não fecha com ESC nem
            clicando fora, para não perder preenchimento em andamento).
        --}}
        <div
            x-show="modalClientesAberto"
            x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center p-0 sm:p-6"
            x-effect="document.documentElement.style.overflow = modalClientesAberto ? 'hidden' : ''"
        >
            <div class="absolute inset-0 bg-raiz-950/70"></div>

            <div
                class="relative flex w-full flex-col overflow-hidden bg-white shadow-2xl transition-[width,height] duration-150"
                x-bind:class="modalClientesTelaCheia ? 'h-full w-full rounded-none' : 'h-full rounded-none sm:h-[80vh] sm:w-[80vw] sm:rounded-2xl'"
            >
                <div class="flex items-center justify-between border-b border-raiz-200 bg-raiz-50 px-5 py-3">
                    <h2 class="font-display text-sm tracking-wide text-raiz-800">CLIENTES</h2>
                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            x-on:click="modalClientesTelaCheia = ! modalClientesTelaCheia"
                            class="rounded-lg p-2 text-raiz-600 transition hover:bg-raiz-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-raiz-500"
                            x-bind:aria-label="modalClientesTelaCheia ? 'Restaurar tamanho' : 'Tela cheia'"
                        >
                            <x-icon name="expand" class="size-5" x-show="! modalClientesTelaCheia" />
                            <x-icon name="collapse" class="size-5" x-show="modalClientesTelaCheia" x-cloak />
                        </button>
                        <button
                            type="button"
                            x-on:click="modalClientesAberto = false"
                            class="rounded-lg p-2 text-raiz-600 transition hover:bg-raiz-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-raiz-500"
                            aria-label="Fechar"
                        >
                            <x-icon name="close" class="size-5" />
                        </button>
                    </div>
                </div>

                <div class="relative flex-1">
                    <div
                        x-show="modalClientesCarregando"
                        x-cloak
                        class="absolute inset-0 z-10 flex items-center justify-center bg-white"
                    >
                        <div class="size-8 animate-spin rounded-full border-2 border-raiz-200 border-t-raiz-700"></div>
                    </div>

                    <iframe
                        x-bind:src="modalClientesAberto ? '{{ route('clientes.index') }}' : 'about:blank'"
                        x-on:load="modalClientesCarregando = false"
                        class="size-full border-0"
                        title="Clientes"
                    ></iframe>
                </div>
            </div>
        </div>
    </body>
</html>
