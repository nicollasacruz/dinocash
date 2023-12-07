<template>
    <UserLayouyt>
        <div class="p-2 lg:px-8 h-full">
            <!-- <div class="text-center uppercase text-xl lg:text-3xl text-gray-800 mb-4">
                Jogar
            </div> -->
            <div
                class="w-full h-full flex-col justify-center flex gap-y-4 text-gray-800"
            >
                <input
                    type="text"
                    class="bg-white mx-auto max-w-xs border-8 rounded-xl border-gray-800 w-full"
                    placeholder="Digite o valor da aposta"
                    v-model="amount"
                />
                <div class="text-center">
                    Aposta mínima: R$ 1,00
                </div>
                <button
                    class="mx-auto py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                    @click="startGame"
                    :disabled="!amount"
                >
                    Jogar
                </button>
            </div>
            <GameCluster
                @end-game="handleEndGame"
                @finish-game="handleFinishGame"
                :active="isRunning"
                :height="clientHeight"
                :width="clientWidth"
                :viciosidade="true"
            />
        </div>
        <BaseModal
            v-if="endGame || finishGame"
            :score="score"
            v-model="endGame"
        >
            <div v-if="endGame" class="text-center text-2xl">
                Game Over! Você andou {{ score }} metros
            </div>
            <div v-else class="text-center text-2xl">
                Parabéns! Você andou {{ score }} metros
            </div>
            <div class="flex justify-center">
                <button
                    v-if="endGame"
                    class="mx-auto mt-5 py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                    @click="endGame = false"
                >
                    OK
                </button>
                <button
                    v-if="finishGame"
                    class="mx-auto mt-5 py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                    @click="finishGame = false"
                >
                    OK
                </button>
            </div>
        </BaseModal>
        <BaseModal v-if="finishGame" :score="score" v-model="finishGame">
            <div class="text-center text-2xl">
                Parabéns! Você andou {{ score }} metros
            </div>
            <div class="flex justify-center">
                <button
                    class="mx-auto mt-5 py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                    @click="finishGame = false"
                >
                    OK
                </button>
            </div>
        </BaseModal>
    </UserLayouyt>
</template>

<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import BaseModal from "../../Components/BaseModal.vue";
import dayjs from "dayjs";
import GameCluster from "../../Components/GameCluster.vue";
import { computed, onMounted, ref, toRef } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import axios from "axios";

const { viciosidade } = defineProps(["viciosidade"]);
const finishGame = ref(false);
const page = usePage();

const user = computed(() => page.props.auth.user.id);
const userId = toRef(user, "userId");
// const props = computed(() => page.props.viciosidade);
// const viciosidade = toRef(props, "viciosidade");
console.log(page.props, "props");
const amount = ref(0);
const isRunning = ref(false);
const gameId = ref(0);
const endGame = ref(false);
const clientHeight = ref(0);
const clientWidth = ref(0);
const difficulty = ref(false);
const score = ref(0);
const type = computed(() => (score.value > 500 ? "win" : "loss"));

async function fetchStore() {
    try {
        const response = await axios.post(route("user.play.store"), {
            amount: amount.value,
        });
        console.log(response);
        const result = response.data.gameHistory.id;

        return result;
    } catch (error) {
        console.error("Erro na pesquisa:", error);

        throw error;
    }
}

async function generateSHA256Hash(input) {
    const encoder = new TextEncoder();
    const data = encoder.encode(input);

    const hashBuffer = await crypto.subtle.digest("SHA-256", data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray
        .map((byte) => byte.toString(16).padStart(2, "0"))
        .join("");

    return hashHex;
}

async function fetchUpdate() {
    // console.log(type.value, 'value');
    try {
        const hash = await generateSHA256Hash(`${gameId.value}${userId.value}dinocash`);
        const response = await axios.patch(route("user.play.update"), {
            distance: score.value,
            gameId: gameId.value,
            type: type.value,
            token: hash,
        });

        const result = response.data;
        console.log(result, "result");
        return result;
    } catch (error) {
        console.error("Erro na pesquisa:", error);

        throw error;
    }
}

async function startGame() {
    // axios.post('/')
    gameId.value = await fetchStore();
    if (gameId.value) {
        const { height, width } = document.body.getBoundingClientRect();
        isRunning.value = true;
        clientHeight.value = height;
        clientWidth.value = width;
        difficulty.value = true;
        const div = document.getElementById("root") as HTMLDivElement;
        div.style.display = "none";
    }
}
const handleEndGame = (pontuation) => {
    console.log(pontuation, "value handle");
    isRunning.value = false;
    endGame.value = true;
    score.value = pontuation;
    type.value = "loss";
    const div = document.getElementById("root") as HTMLDivElement;
    div.style.display = "block";
    fetchUpdate();
};

const handleFinishGame = (pontuation) => {
    console.log(pontuation, "value handle");
    isRunning.value = false;
    endGame.value = true;
    score.value = pontuation;
    type.value = "win";
    const div = document.getElementById("root") as HTMLDivElement;
    div.style.display = "block";
    fetchUpdate();
};
// onMounted(() => {
//     const { height, width } = document.body.getBoundingClientRect();

//     clientWidth.value = width;
// });
</script>
