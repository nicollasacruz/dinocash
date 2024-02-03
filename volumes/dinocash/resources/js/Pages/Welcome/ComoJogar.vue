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
        class="h-screen pt-8 lg:p-8 lg:px-14 relative"
    >
        <div
            class="text-4xl sm:text-5xl pl-3 md:text-6xl font-menu font-bold mb-7 lg:mb-8 text-verde lg:text-right relative"
        >
            Como Jogar?
        </div>
        <div class="flex flex-col-reverse lg:flex-row">
            <div class="flex-1 justify-center flex w-full">
                <img :src="dinoJogar" class="w-56 lg:w-96 absolute bottom-0" />
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

                    <div class="text-sm sm:text-sm md:text-lg -mt-2">
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
    "Faça seu cadastro, ou então faça login se você já tem uma conta cadastrada no Dino Cash.",
    "Faça um depósito de no mínimo R$30,00 e no máximo R$10.000,00 para iniciar a sua jornada.",
    "Clique em jogar e escolha o valor da sua aposta para iniciar o jogo, o seu objetivo é pular todos os cactos e não deixar o Dino  bater em nenhum deles.",
    "Após 500 metros percorrido, a tela ficará dourada, a partir dai o seu lucro será contabilizado e você pode sacar a qualquer momento Clicando no botão de parar.",
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
