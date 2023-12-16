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
    @inertia
</body>

</html>

<script>
    navigator.serviceWorker.register("/sw.js");
    document.addEventListener('notify', function({detail}) {
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
</script>