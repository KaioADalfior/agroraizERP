<x-layouts.guest title="Entrar">
    <div class="flex min-h-screen flex-col lg:flex-row">

        {{-- Painel da marca (topo no celular / lado esquerdo no computador) --}}
        <section class="relative flex items-center justify-center overflow-hidden bg-raiz-700 px-6 py-10 lg:w-1/2 lg:px-16 lg:py-16 xl:w-[55%]">
            <div class="pointer-events-none absolute inset-0 bg-linear-to-br from-raiz-600/40 via-transparent to-raiz-900/50"></div>
            <x-curvas class="text-white/15" />

            <div class="relative flex flex-col items-center text-center">
                <x-logo class="w-44 sm:w-52 lg:w-full lg:max-w-md" />

                <p class="mt-8 hidden max-w-sm text-sm leading-relaxed text-white/70 lg:block">
                    Gestão de clientes e análises de solo, com interpretação clara para decisões melhores no campo.
                </p>
            </div>

            <p class="absolute bottom-6 hidden text-xs text-white/50 lg:block">
                © {{ date('Y') }} Agro Raiz — Consultorias e Projetos
            </p>
        </section>

        {{-- Formulário --}}
        <section class="flex flex-1 items-center justify-center px-6 py-12 sm:px-10">
            <div class="w-full max-w-sm">
                <h1 class="font-display text-xl tracking-wide text-raiz-800">Entrar</h1>
                <p class="mt-2 text-sm text-raiz-600">Use seu e-mail e senha para acessar o sistema.</p>

                <form
                    method="POST"
                    action="{{ route('login.store') }}"
                    class="mt-8 space-y-5"
                    x-data="{ enviando: false }"
                    x-on:submit="enviando = true"
                >
                    @csrf

                    <x-input
                        name="email"
                        type="email"
                        label="E-mail"
                        icon="envelope"
                        placeholder="voce@exemplo.com"
                        autocomplete="username"
                        autofocus
                        required
                    />

                    <x-input
                        name="password"
                        type="password"
                        label="Senha"
                        icon="lock"
                        placeholder="Sua senha"
                        autocomplete="current-password"
                        required
                    />

                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-raiz-700">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-raiz-300 accent-raiz-700" @checked(old('remember'))>
                        Manter conectado neste computador
                    </label>

                    <x-button type="submit" class="w-full" x-bind:disabled="enviando">
                        <span x-show="! enviando">Entrar</span>
                        <span x-show="enviando" x-cloak>Entrando…</span>
                    </x-button>
                </form>

                <p class="mt-8 text-center text-sm text-raiz-500">
                    Precisa de acesso? Fale com o administrador do sistema.
                </p>
            </div>
        </section>
    </div>
</x-layouts.guest>
