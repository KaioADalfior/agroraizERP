{{-- Layout para páginas públicas (login). --}}
@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        @include('partials.head', ['title' => $title])
    </head>
    <body class="min-h-full bg-raiz-50 font-sans text-raiz-900 antialiased">
        {{ $slot }}
    </body>
</html>
