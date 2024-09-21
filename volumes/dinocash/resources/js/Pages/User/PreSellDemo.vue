<script setup>
function gerarNumeroAleatorio(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

const numeroAleatorio = gerarNumeroAleatorio(20000, 25000);

function getUrlParams() {
    const params = new URLSearchParams(window.location.search);
    const utm_source = params.get('utm_source');
    const utm_campaign = params.get('utm_campaign');
    const utm_medium = params.get('utm_medium');
    const utm_content = params.get('utm_content');

    if (utm_source && utm_campaign && utm_medium && utm_content) {
        const utmData = {
            source: utm_source,
            campaign: utm_campaign,
            medium: utm_medium,
            content: utm_content
        };

        // Armazenar no localStorage e sessionStorage
        localStorage.setItem('utmData', JSON.stringify(utmData));
        sessionStorage.setItem('utmData', JSON.stringify(utmData));
    }
}
if (window.location.host === 'dinofeliz.com') {

    getUrlParams();
}

</script>

<template>
    <div class="divcentralmed">
        <div class="div2">
            <div class="image">
                <img style="width: 4rem;" src="https://dinodogoolgle.online/wp-content/uploads/2024/08/android-chrome-512x512-1.png" alt="">
            </div>
            <div style="background-color: #191919;border-radius: 5px;padding: 0.4rem;">
                <div class="saldoNumerb">
                    SALDO:
                </div>
                <div class="numbersaldo">
                    R$100,00
                </div>
            </div>
            <div class="text-01">
                <h1>JOGUE AGORA</h1>
            </div>
            <div class="text-02">
                <span>Divirta-se e lucre!</span>
            </div>
            <div class="text-03">
                <div style="width: 100%;display: flex;justify-content: center;">
                    <span>Valor da entrada:</span>
                </div>
                <div class="entrada">
                    R$50,00
                </div>
                <div>
                    <a href="/demo">
                        <button>Iniciar Rodada!</button>
                    </a>
                </div>
            </div>
            <div class="text-04">
                <div class="text-black">
                    Jogadores Online : <b id="spanumber">{{ numeroAleatorio }}</b>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
*{
    padding: 0;
    font-family: 'Poppins', sans-serif;
    margin: 0;
}
.divcentralmed .div2 .image{
    display: flex;
    justify-content: center;
    position: absolute;
    top: -2rem;
    left: 50%;
    transform: translate(-50%);
}
.divcentralmed .div2{
    position: fixed;
    padding: 0.7rem;
    left: 50%;
    background-color: #1f1f1f;
    top: 50%;
    transform: translate(-50%,-50%);
    border-radius: 10px;
    border: 3px solid #C960FF;
    width: 80%;
}
.divcentralmed{
    background-image: url('https://dinofeliz.com/build/assets/bg-login-1d6160df.jpg');
    background-position: center;
    background-size: cover;
    width: 100vw;
    height: 100vh;
}
.text-01{
    margin-top: 0.1rem;
}
.text-02{
    color: #ccc;
    text-align: center;
}
.text-01 h1{
    color: #fff;
    text-align: center;
}
.text-03{
    margin-top: 0.89rem;
}
.text-03 span{
    color: #eee;
    font-weight: 900;
    text-align: center;
}
.text-03 button{
    width: 100%;
    padding: 0.7rem;
    border-radius: 5px;
    color: #1f1f1f;
    font-weight: 900;
    background-color: #c960ff;
    margin-top: 1rem;
    font-size: 1.1rem;
}
.text-04 div{
    background-color: #ccc;
    margin-top: 1rem;
    padding: 0.4rem;
    border-radius: 5px;
    text-align: center;
    font-weight: 500;
}
.entrada{
    padding: 0.4rem;
    border: 2px dashed #C960FF;
    border-radius: 5px;
    color: #fff;
    font-size: 1.6rem;
    font-weight: 900;
    text-align: center;
}
.saldoNumerb{
    color: #ccc;
    text-align: center;
    margin-top: 1.5rem;
}
.numbersaldo{
    margin-top: -0.4rem;
    text-align: center;
    font-size: 1.6rem;
    font-weight: 700;
    color: #ccc;
}
</style>
