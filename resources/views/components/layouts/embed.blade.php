{{--
    Layout leve, sem menu lateral, cabeçalho ou rodapé do painel — usado
    quando a página é carregada dentro de um iframe (ex.: o modal de
    Clientes aberto a partir do menu principal). Mesma identidade visual
    (fontes, cores), só sem a "moldura" do app, que já existe no modal.
    Uso: <x-layouts.embed title="Clientes"> ...conteúdo... </x-layouts.embed>
--}}
@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        @include('partials.head', ['title' => $title])
    </head>
    <body class="min-h-full bg-raiz-50 font-sans text-raiz-900 antialiased">
        <main class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </body>
</html>
