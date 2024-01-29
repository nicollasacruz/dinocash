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
        <div class="text-4xl md:text-6xl text-verde font-menu text-center md:text-right mb-5">
            Recompensas
        </div>
        <div class="flex flex-col items-center md:flex-row">
            <div class="md:flex-1 w-full bg-white h-52 md:mr-5 mb-3"></div>
            <div class="flex flex-1 w-full justify-end">
                <div
                    class="md:text-lg max-h-[60%] text-right mt-5 text-white mb-2 w-96 md:max-w-md"
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
