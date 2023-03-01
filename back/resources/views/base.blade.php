<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        {{-- <link href="{{ asset('css/reset.css') }}" rel="stylesheet"> --}}
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
         <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="antialiased">
    {{-- vueコンポーネント --}}
    <div id="app">
        <hello-world></hello-world>
    </div>
    <p>テスト</p>
    <!-- Scripts -->
    <script src=" {{ mix('js/vendor.js') }} "></script>
    </body>
</html>
