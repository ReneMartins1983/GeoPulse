<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GeoPulse — Monitoramento de frota em tempo real</title>

    {{-- Open Graph / prévia em redes sociais --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="GeoPulse">
    <meta property="og:title" content="GeoPulse — Painel de frota em tempo real">
    <meta property="og:description" content="Dashboard de monitoramento de frota com mapa interativo (Leaflet) e gráficos (Chart.js) sobre uma API REST.">
    <meta property="og:image" content="{{ url('/og-image.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-950">
    <div id="app"></div>
</body>
</html>
