<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'StudioForge' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="app-body">
    <header class="topbar">
        <a class="brand" href="{{ auth()->check() ? route('dashboard') : route('login') }}"><span>SF</span> StudioForge</a>
        @auth
            <div class="topbar-actions"><span>{{ auth()->user()->name }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button class="button ghost">Sign out</button></form></div>
        @endauth
    </header>
    @if(session('status'))<div class="toast" role="status">{{ session('status') }}</div>@endif
    <main>{{ $slot }}</main>
@livewireScripts
</body>
</html>
