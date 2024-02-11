<template>
    <section
        :style="{
            backgroundImage: `url('${
                windowWidth < 700 ? backMobile : background
            }')`,
            backgroundSize: windowWidth < 700 ? 'cover' : 'cover',
            backgroundPosition: 'center',
            backgroundRepeat: 'no-repeat',
        }"
        class="h-screen pt-4 lg:p-8 lg:px-14 relative"
    >
        <div
            class="text-4xl sm:text-5xl pl-3 md:text-6xl font-menu font-bold mb-5 lg:mb-8 text-verde lg:text-right relative"
        >
            Como Jogar?
        </div>
        <div class="flex flex-col-reverse lg:flex-row">
            <div class="flex-1 justify-center flex w-full pt-5">
                <img :src="dinoJogar" class="shrink-image w-44 lg:w-96 absolute bottom-0 " />
            </div>
            <div class="flex-1">
                <div
                    class="flex mb-3 md:mb-5 items-start px-3"
                    v-for="(instruction, i) in instructions"
                    :key="i"
                >
                    <div
                        class="w-9 h-9 lg:w-11 lg:h-11 shrink-0 font-menu font-bold rounded text-center bg-verde py-1 text-roxo text-3xl md:text-4xl flex items-center justify-center mr-5"
                    >
                        {{ i + 1 }}
                    </div>

                    <div class="text-sm shrink-text sm:text-sm md:text-lg -mt-2">
                        {{ instruction }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref } from "vue";
import background from "../../../../storage/imgs/home-page/bg3-desk.jpg";
import backMobile from "../../../../storage/imgs/home-page/bg3-mobile.jpg";

import dinoJogar from "../../../../storage/imgs/home-page/dino-jogar.svg";
console.log(dinoJogar);
const instructions = [
    "Faça seu cadastro ou então logue-se no site você já tem uma conta cadastrada no Dino Cash.",
    "Faça um depósito de no mínimo R$30,00 para iniciar a sua jornada.",
    "Clique em jogar e escolha o valor da sua aposta para iniciar o jogo, o seu objetivo é pular todos os cactos e não deixar o Dino bater em nenhum deles.",
    "Após 500 metros percorrido, a tela ficará de noite, a partir dai o seu lucro será multiplicado e você pode sacar a qualquer momento clicando no botão de retirar lucro.",
    "Se você for um jogador habilidoso, você terá a chance de ganhar recompensas de ícones exclusivos após os 1.000 metros."
];
const { canLogin, canRegister, laravelVersion, phpVersion, rankedUsers } =
    defineProps({
        canLogin: {
            type: Boolean,
        },
        canRegister: {
            type: Boolean,
        },
        laravelVersion: {
            type: String,
            required: true,
        },
        phpVersion: {
            type: String,
            required: true,
        },
        rankedUsers: {
            type: Array,
            required: true,
        },
    });
const windowWidth = ref(window.innerWidth);

window.addEventListener("resize", (valu) => {
    windowWidth.value = window.innerWidth;
});
</script>
<style>
.shrink-image {
    @media(max-width: 321px) {
        width: 110px;
    }
}
.shrink-text {
    @media(max-width: 321px) {
        font-size: .79em;
    }
}
</style>