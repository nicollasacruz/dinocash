<template>
    <section
        id="section2"
        class="section2 h-screen mx-auto p-6 lg:p-10 pb-5"
        :style="{
            backgroundImage: `url('${background}')`,
            backgroundSize: 'cover',
            backgroundPosition: windowWidth < 700 ? 'bottom' : 'center',
            backgroundRepeat: 'no-repeat',
        }"
    >
        <div
            class="text-4xl md:text-6xl text-verde font-menu text-center md:text-right lg:mr-40 "
        >
            Recompensas
        </div>
        <div class="flex flex-col items-center md:items-start md:flex-row lg:mr-40">
            <div class="flex-1 w-full md:mr-5 flex justify-center md:justify-end">
                <img :src="reward" class="max-w-sm md:max-w-xl md:float-right w-full md:-mt-8" />
            </div>
            <div class="flex justify-center md:justify-end w-full md:w-auto">
                <div
                    class="md:text-lg max-h-[60%] text-right mb-2 w-96 md:max-w-md md:mt-5"
                >
                    <div>
                        Jogue DinoCash e ganhe recompensas exclusivas, são
                        vários prêmios que você poderá adquirir somente jogando
                        ou depositando!
                    </div>
                    <div class="text-verde font-menu text-lg my-3">
                        <div>- Ícones de perfil lendários.</div>
                        <div>- Cashback de 5% em seus depósitos.</div>
                        <div>- Rodadas grátis toda semana.</div>
                    </div>
                    <div>
                        Não fique de fora e não perca a chance de lucrar e muito
                        com DinoCash, tudo depende apenas da sua habilidade,
                        nada de sorte!
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import background from "../../../../storage/imgs/home-page/bg.jpg";
import reward from "../../../../storage/imgs/home-page/reward.svg";

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
