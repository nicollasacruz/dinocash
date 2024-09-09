<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json"/>
    <meta name="description" content="Dinofeliz - Lucrando com suas próprias habilidades">
    <meta name="keywords"
          content="dinofeliz, cash, tigrinho, fortune, aviãozinho, pgsoft, tiger, ox, dino, dino run, dino-run, trex, bet, renda extra, renda, renda-extra, dinheiro, rico, jogo, game, cassino">
    <title inertia>{{ config('app.name', 'Dinofeliz | Lucre com a gente') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Scripts -->
{{--    <script async src="//code.jivosite.com/widget/IgclMWygKX"></script>--}}

    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-menu antialiased">
<meta name="csrf-token" content="{{ csrf_token() }}">
@inertia
</body>

</html>

<script src="/enable-push.js" defer></script>
<script>
    navigator.serviceWorker.register("/sw.js");
    document.addEventListener('notify', function ({detail}) {
        initSW();
    });
</script>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-Z43MJ7SN7Q"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'G-Z43MJ7SN7Q');
</script>

<!-- Facebook Pixel Code -->
<script>
    !function (f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function () {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1629210450974389');
    fbq('track', 'PageView');
</script>
<noscript>
    <img height="1" width="1" style="display:none"
         src="https://www.facebook.com/tr?id=1629210450974389&ev=PageView&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap');

    @font-face {
        font-family: "Inter", sans-serif;
        src: url("inter/Inter-Bold.ttf") format("opentype");

    }
</style>
