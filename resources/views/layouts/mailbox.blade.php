<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('clarky.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee&display=swap" rel="stylesheet">
</head>

<body>
    <div class="lg:grid grid-cols-[256px_1fr] grid-rows-1 h-screen">
        <x-nav-sidemenu />

        <main class="bg-primary flex flex-col">
            <x-header-mailbox />
            <div class="flex-1 bg-white lg:rounded-tl-4xl border-t-2 lg:border-l-2 border-third">
                @yield('mailbox.content')
            </div>
            <x-compose />
        </main>
    </div>
    <x-flash-message />
</body>

</html>