<template>
    <section
        id="section2"
        class="section2 h-screen mx-auto p-1 lg:p-10 flex items-center justify-center z-0"
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
        <!-- Modal -->
        <BaseModal v-model="showModal" v-if="showModal" class="modal z-50">
            <div class="modal-content ">
                <div class="text-3xl text-verde font-bold text-center mb-3">Recompensa Obtida</div>
                <!-- Replicar a div onde o seletor parou -->
                <div class="bg-[#17111F] w-20 mx-auto">
                    <div
                        class="text-center flex w-20 h-20 flex-col items-center justify-center col-span-1 border-roxo border my-1 md:my-3 rounded-lg"
                        v-if="selectedPremio"
                    >
                        <div
                            class="text-lg sm:text-2xl lg:text-3xl text-verde font-bold"
                        >
                            {{ selectedPremio.label }}
                        </div>
                        <div
                            class="leading-none text-xs sm:text-sm md:text-base md:leading-tight text-white uppercase font-bold"
                        >
                            {{ selectedPremio.complement }}
                        </div>
                    </div>
                </div>
                <button class="user-button mx-auto mt-5" @click="closeRoullete">Coletar</button>
            </div>
        </BaseModal>
    </section>
</template>

<script setup>
import pinoRoleta from "../../../../storage/imgs/user/pino-roleta.svg";
import background from "../../../../storage/imgs/user/bg-roleta.jpg";
import bgMobile from "../../../../storage/imgs/user/bg-roleta-mobile.jpg";
import { ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import axios from "axios";
import BaseModal from "@/Components/BaseModal.vue";

const position = ref(50);
const loading = ref(false);
const showModal = ref(false);
const selectedPremio = ref(0);
const windowWidth = ref(window.innerWidth);
const selectedIndex = ref();
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
    console.log(randomStop);
    const index = getPosition(randomStop);
    selectedIndex.value = index;
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

    postRecompensa(index);
};

function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}
window.addEventListener("resize", (valu) => {
    windowWidth.value = window.innerWidth;
});

function closeRoullete() {
    showModal.value = false;
    router.get(route("user.play"));
}

const postRecompensa = async (rewardOption) => {
    try {
        const page = usePage();
        let user = page.props.auth.user.id;
        console.log("úserID", user);

        const response = await axios.post(route("user.recompensa.roleta"), {
            user: user,
            rewardOption: rewardOption,
        });

        console.log("Recompensa enviada com sucesso:", response.data);
        if (response.data.status === "success") {
            showModal.value = true;
            console.log(premios);
            console.log(selectedIndex.value);
            selectedPremio.value = premios[selectedIndex.value - 1];
            console.log(selectedPremio.value, "premio");
        }
    } catch (error) {
        console.error("Erro ao enviar a recompensa:", error.message);
        alert("Erro ao coletar a recompensa");
        // location.reload()
    }
};
</script>
<style>
@font-face {
    font-family: "Inter", sans-serif;
    src: url("inter/Inter-Bold.ttf") format("opentype");
}
</style>
