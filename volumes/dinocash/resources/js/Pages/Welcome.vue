<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import Background1 from '../../../storage/imgs/home-page/home-bg1.jpg';
import Background2 from '../../../storage/imgs/home-page/home-bg2.jpg';
import Background3 from '../../../storage/imgs/home-page/home-bg3.jpg';
import Background4 from '../../../storage/imgs/home-page/home-bg3.jpg';
import DinoLogo from '../../../storage/imgs/home-page/dino-logo.svg';
import DinoInterrogacao from '../../../storage/imgs/home-page/dino-interrogacao-bg3.svg';
import DinoRoxo from '../../../storage/imgs/home-page/dino-roxo.svg';

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
});
</script>

<template>
    <Head title="Home" />
    <BaseLayout>
        <section id="section1" class="section1 w-screen h-screen mx-auto items-center">
            <div class="content mx-auto max-w-[1920px] flex flex-col justify-end"
                :style="{ backgroundImage: `url('${Background1}')` }">
                <img :src="DinoLogo" alt="dinoLogo" class="h-[45%] mb-[10vh]">
                <Link
                    class="mx-auto mb-4 flex justify-center items-center w-[300px] h-[80px] bg-verde-claro rounded-lg font-menu text-3xl text-roxo-fundo boxShadow border-black border-4"
                    :href="route('register')">
                        JOGAR AGORA
                </Link>
                <span class="mx-auto mb-[10vh] text-xl font-menu text-white">
                    CADASTRE-SE E OBTENHA RODADAS GRÁTIS
                </span>
            </div>
        </section>

        <section id="section2" class="section2 h-screen mx-auto">
            <div class="content mx-auto max-w-[1920px]" :style="{ backgroundImage: `url('${Background2}')` }"> teste</div>
        </section>

        <section id="section3" class="section3 h-screen mx-auto">
            <div class="content mx-auto max-w-[1920px]" :style="{ backgroundImage: `url('${Background3}')` }"> teste</div>
        </section>

        <section id="section4" class="section4 h-screen mx-auto">
            <div class="content mx-auto max-w-[1920px]" :style="{ backgroundImage: `url('${Background4}')` }"> teste</div>
        </section>

        <footer></footer>
    </BaseLayout>
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
  box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.75);
  -webkit-box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.75);
  -moz-box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.75);
}

.content {
    max-width: 1920px;
    height: 100%;
    background-size: auto 100vh;
    background-repeat: no-repeat;
    background-position: center;
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
