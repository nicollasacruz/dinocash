<script setup>
import { ref } from "vue";
import Abertura from "./Welcome/Abertura.vue";
import About from "./Welcome/About.vue";
import ComoJogar from "./Welcome/ComoJogar.vue";
import Ranking from "./Welcome/Ranking.vue";
import Reward from "./Welcome/Reward.vue";

import Footer from "./Welcome/Footer.vue";
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

<template>
    <Abertura />
    <About />
    <ComoJogar />
    <Ranking :ranked-users="rankedUsers" />
    <Reward />
    <Footer />
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}

.boxShadow {
    box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.85);
    -webkit-box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.85);
    -moz-box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.85);
}

.content {
    max-width: 1920px;
    height: 100%;
}

.section1 {
    background-color: rgb(108, 86, 124);
}

.section2 {
    background-color: rgb(144, 188, 175);
}

.section3 {
    background-color: rgb(240, 225, 168);
}

.section4 {
    background-color: rgb(240, 225, 168);
}
</style>
