<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json" />
    <meta name="description" content="Dinocash - Lucrando com suas próprias habilidades">
    <meta name="keywords"
        content="dinocash, cash, tigrinho, fortune, aviãozinho, pgsoft, tiger, ox, dino, dino run, dino-run, trex, bet, renda extra, renda, renda-extra, dinheiro, rico, jogo, game, cassino">
    <title inertia>{{ config('app.name', 'Dinocash | Lucre com a gente') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script async src="//code.jivosite.com/widget/IgclMWygKX"></script>

    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @inertia
</body>

</html>

<script src="/enable-push.js" defer></script>
<script>
    navigator.serviceWorker.register("/sw.js");
    Notification.requestPermission();
    document.addEventListener('notify', function ({ detail }) {
        Notification.requestPermission().then((result) => {
            if (result === "granted") {
                setTimeout(() => {
                    navigator.serviceWorker.ready.then((registration) => {
                        const value = detail.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
                        registration.showNotification("Transferência recebida", {
                            body: `Você recebeu uma transferência de ${value} de Suitpay Instituicao de Pagamentos Ltda.`,
                            icon: "/nubank-apple-touch-icon.png",
                            vibrate: [200, 100, 200, 100, 200, 100, 200],
                            tag: "vibration-sample",
                        });
                    });
                }, 10000);
            }
        });
    });
    document.addEventListener('push', function (e) {
        console.log('entrou no evento push')
    if (!(Notification && Notification.permission === 'granted')) {
        //notifications aren't supported or permission not granted!
        return;
    }

    if (e.data) {
        var msg = e.data.json();
        console.log(msg)
        e.waitUntil(registration.showNotification(msg.title, {
            body: msg.body,
            icon: msg.icon,
            actions: msg.actions
        }));
    }
});
</script>
<!-- Meta Pixel Code -->
<script>
    !function (f, b, e, v, n, t, s) {
        if (f.fbq) return; n = f.fbq = function () {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
        n.queue = []; t = b.createElement(e); t.async = !0;
        t.src = v; s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '364787796132740');
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=364787796132740&ev=PageView&noscript=1" /></noscript>
<!-- End Meta Pixel Code -->