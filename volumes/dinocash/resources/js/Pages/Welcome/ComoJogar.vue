<template>
    <section
        :style="{
            backgroundImage: `url('${background}')`,
            backgroundSize: windowWidth < 700 ? 'auto 100vh' : 'cover',
            backgroundPosition: 'center',
            backgroundRepeat: 'no-repeat',
        }"
        class="h-screen pt-8 lg:p-8 lg:px-14"
    >
        <div class="flex">
            <div class="flex-1 hidden lg:block"></div>
            <div class="flex-1">
                <div
                    class="text-5xl pl-3 md:text-6xl font-menu mb-5 lg:mb-8 text-verde lg:text-right"
                >
                    Como Jogar?
                </div>
                <div
                    class="flex mb-5 items-start px-3"
                    v-for="(instruction, i) in instructions"
                    :key="i"
                >
                    <div
                        class="w-11 h-11 shrink-0 font-menu rounded text-center bg-verde pt-1 text-roxo text-4xl flex items-center justify-center mr-5"
                    >
                        {{ i + 1 }}
                    </div>

                    <div class="md:text-lg -mt-2">
                        {{ instruction }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref } from "vue";
import background from "../../../../storage/imgs/home-page/bg.jpg";

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
