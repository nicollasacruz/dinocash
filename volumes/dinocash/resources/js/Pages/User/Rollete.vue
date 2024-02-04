<template>
    <section
        id="section2"
        class="section2 h-screen mx-auto p-1 lg:p-10 flex items-center justify-center"
        :style="{
            backgroundImage: `url('${
                windowWidth < 700 ? bgMobile : background
            }')`,
            backgroundSize: 'cover',
            backgroundPosition: windowWidth < 700 ? 'bottom' : 'center',
            backgroundRepeat: 'no-repeat',
        }"
    >
        <div class="max-w-xl lg:max-w-2xl w-full">
            <div class="text-5xl font-bold text-verde text-center mb-3">
                Dino Bônus
            </div>
            <div class="text-center text-white text-sm md:text-base">
                Parabéns, você está elegível para receber um <br />
                bônus grátis! Gire a roleta abaixo para <br />
                resgatar o seu bônus.
            </div>
            <div
                class="grid grid-cols-5 bg-[#17111F] h-20 sm:h-24 md:h-28 flex-1 my-10 gap-x-1 lg:gap-x-2 px-1 md:px-3 relative"
            >
                <img
                    :src="pinoRoleta"
                    class="absolute h-24 sm:h-28 md:h-32 lg:h-40 -top-2 lg:-top-6 w-[2px] md:w-[4px]"
                    :style="`left: ${position}%`"
                />
                <div
                    class="text-center flex flex-col items-center justify-center col-span-1 border-roxo border my-1 md:my-3 rounded-lg"
                    v-for="({ label, complement }, i) in premios"
                    :key="i"
                >
                    <div
                        class="text-lg sm:text-2xl lg:text-3xl text-verde font-bold"
                    >
                        {{ label }}
                    </div>
                    <div
                        class="leading-none text-xs sm:text-sm md:text-base md:leading-tight text-white uppercase font-bold"
                    >
                        {{ complement }}
                    </div>
                </div>
            </div>
            <button
                :disabled="loading"
                @click="girarRoleta"
                class="user-button w-60 mx-auto"
            >
                Girar
            </button>
        </div>
    </section>
</template>

<script setup>
import pinoRoleta from "../../../../storage/imgs/user/pino-roleta.svg";
import background from "../../../../storage/imgs/user/bg-roleta.jpg";
import bgMobile from "../../../../storage/imgs/user/bg-roleta-mobile.jpg";
import { ref } from "vue";
const position = ref(50);
const loading = ref(false);
const windowWidth = ref(window.innerWidth);
const premios = [
    {
        label: "R$100",
        complement: "saldo",
        from: 1,
        to: 3,
    },
    {
        label: "5 ",
        complement: "Rodadas Grátis",
        from: 4,
        to: 10,
    },
    {
        label: "R$20",
        complement: "saldo",
        from: 11,
        to: 20,
    },
    {
        label: "2",
        complement: "Rodadas Grátis",
        from: 21,
        to: 30,
    },
    {
        label: "R$ 5",
        complement: "saldo",
        from: 31,
        to: 100,
    },
];
const getPosition = (value) => {
    const index = premios.findIndex(({ to, from }) => {
        if (value >= from && value <= to) return true;
        else return false;
    });
    return index + 1;
};
const girarRoleta = async () => {
    loading.value = true;
    const randomStop = Math.floor(Math.random() * 100) + 1;
    const index = getPosition(randomStop);
    const posicaoMaxima = index * 20 - 2;
    const posicaoMinima = index * 20 - 20 + 2;
    const posicaoFinal =
        Math.floor(Math.random() * (posicaoMaxima - posicaoMinima)) +
        posicaoMinima;
    let speed = 1000; // velocidade padrão

    for (let i = 0; i < 2; i++) {
        for (let j = 1; j <= 100; j++) {
            position.value = j;
            await sleep(speed / 100);
        }
        for (let j = 100; j >= 1; j--) {
            position.value = j;
            await sleep(speed / 100);
        }
    }

    speed = 10 * posicaoFinal; // ajusta a velocidade baseada no número aleatório
    for (let j = 1; j <= posicaoFinal; j++) {
        position.value = j;
        await sleep(speed / posicaoFinal);
    }
    loading.value = false;
};

function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}
window.addEventListener("resize", (valu) => {
    windowWidth.value = window.innerWidth;
});
</script>
<style>
@font-face {
    font-family: "Inter", sans-serif;
    src: url("inter/Inter-Bold.ttf") format("opentype");
}
</style>
