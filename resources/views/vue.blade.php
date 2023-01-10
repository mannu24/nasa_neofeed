<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Nasa Neo Feed</title>
        <link rel="icon" type="image/png" href="/logo.png">
        <script src="{{ asset('plug/jquery/jquery.min.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('plug/sweetalert2/sweetalert2.min.css')}}">
        <script src="{{ asset('plug/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    </head>
    <body>
        <div id="root">
            <router-view></router-view>
        </div>
    </body>
</html>
