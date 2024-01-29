<template>
    <section
        id="section1"
        class="section1 h-screen mx-auto items-center pt-4 px-4 md:p-2 flex flex-col"
        :style="{
            backgroundImage: `url('${
                windowWidth < 700 ? bgMobile : background
            }')`,
            backgroundSize: 'cover',
            backgroundPosition: windowWidth < 700 ? 'bottom' : 'center',
            backgroundRepeat: 'no-repeat',
        }"
    >
        <div
            v-if="$page.props.errors.banned"
            class="absolute box p-6 text-red-500 text-6xl uppercase font-bold flex items-center justify-center"
            style="background-color: rgba(0, 0, 0, 0.8)"
        >
            {{ $page.props.errors.banned }}
        </div>

        <div class="my-3 md:my-10 lg:w-[550px] px-3">
            <div class="flex sm:justify-center lg:justify-start">
                <img
                    :src="DinoLogo"
                    alt="dinoLogo"
                    class="w-full sm:w-80 lg:w-10/12 -ml-2"
                />
            </div>
            <div class="text-white text-lg">
                Seja bem-vindo(a) ao <b class="text-verde">DinoCash!</b><br />
                O jogo que você não precisa <br class="hidden" />
                contar com a sua <br class="hidden lg:block" />
                sorte,
                <span class="text-verde">
                    apenas <br class="hidden" />
                    com sua habilidade.
                </span>
            </div>
            <div
                v-if="!!!$page?.props?.auth?.user?.id"
                class="w-full mt-8 flex flex-col lg:flex-row items-center lg:justify-start gap-2 lg:gap-6"
            >
                <Link
                    class="mb-4 lp-button bg w-full lg:w-[220px] h-[65px]"
                    :href="route('login')"
                    >{{ __("auth.login") }}</Link
                >

                <Link
                    class="mb-4 lp-button bg w-full lg:w-[220px] h-[65px]"
                    :href="route('register')"
                >
                    {{ __("auth.register") }}
                </Link>
            </div>
            <div
                class="w-full mt-8 flex flex-col lg:flex-row items-center lg:justify-start gap-2 lg:gap-6"
                v-else
            >
                <Link
                    class="mb-4 lp-button bg w-full lg:w-[220px] h-[65px]"
                    :href="route('user.deposito')"
                >
                    DEPOSITAR
                </Link>
                <Link
                    class="mb-4 lp-button bg w-full lg:w-[220px] h-[65px]"
                    :href="route('user.play')"
                >
                    {{ __("homepage.play-now") }}
                </Link>
            </div>
            <div class="mb-[10vh] text-lg text-white text-start mt-3">
                {{ __("homepage.register-tip") }}
            </div>
        </div>
    </section>
</template>
<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import background from "../../../../storage/imgs/home-page/bg.jpg";
import bgMobile from "../../../../storage/imgs/home-page/bg-mobile.jpg";

import DinoLogo from "../../../../storage/imgs/home-page/dino-logo.svg";

const windowWidth = ref(window.innerWidth);

window.addEventListener("resize", (valu) => {
    windowWidth.value = window.innerWidth;
});
</script>
