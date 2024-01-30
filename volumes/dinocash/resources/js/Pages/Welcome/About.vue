<template>
    <section
        id="section2"
        class="section2 h-screen mx-auto p-6 lg:p-10 pb-5"
        :style="{
            backgroundImage: `url('${
                windowWidth < 700 ? background : background
            }')`,
            backgroundSize: 'cover',
            backgroundPosition: windowWidth < 700 ? 'bottom' : 'center',
            backgroundRepeat: 'no-repeat',
        }"
    >
        <div class="text-5xl md:text-6xl text-verde font-menu text-start">Dino Cash</div>
        <div class="flex flex-col lg:flex-row">
            <div
                class="text-sm md:text-lg max-h-[60%] text-justify mt-3 md:mt-5 flex-1 mb-2 lg:mr-5"
            >
                Bem-vindo ao Dinocash, sua principal opção de entretenimento e
                lucratividade em jogos online! Como uma provedora totalmente
                legalizada em Maita, oferecemos uma experibncia única e
                inovadora no mercado. Explore um universo de diversão e
                oportunidades de lucro como nunca antes, desafiando seus
                reflexos em nossos emocionantes jogos. Junte-se a nós para uma
                jornada envolvente, onde o entretenimento se encontra com a
                chance de ganhar grandes recompensas. Venha fazer , parte dessa
                experiência exctusiva no Dinocash, onde a diversão e 05 ganhos ,
                estão ao seu alcance.
            </div>
            <div class="lg:flex-1 bg-white h-52"></div>
        </div>
    </section>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import bgMobile from "../../../../storage/imgs/home-page/bg-mobile.jpg";

import background from "../../../../storage/imgs/home-page/bg.jpg";
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
