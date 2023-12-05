<template>
    <UserLayouyt>
        <div class="p-2 lg:px-8 h-full">
            <!-- <div class="text-center uppercase text-xl lg:text-3xl text-gray-800 mb-4">
                Jogar
            </div> -->
            <div
                class="w-full h-full flex-col justify-center flex gap-y-4 text-gray-800"
            >
                <button
                    class="mx-auto py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                    @click="startGame"
                >
                    Jogar
                </button>
            </div>
            <GameCluster
                @end-game="handleEndGame"
                :active="isRunning"
                :height="clientHeight"
                :width="clientWidth"
            />
        </div>
        <BaseModal v-if="endGame" :score="score" v-model="endGame">
            <div class="text-center text-2xl">Você fez {{ score }} pontos</div>
            <div class="flex justify-center">
                <button
                    class="mx-auto mt-5 py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                    @click="endGame = false"
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
import { onMounted, ref } from "vue";
const isRunning = ref(false);
const endGame = ref(false);
const clientHeight = ref(0);
const clientWidth = ref(0);
const score = ref(0);
function startGame() {
    const { height, width } = document.body.getBoundingClientRect();
    isRunning.value = true;
    clientHeight.value = height;
    clientWidth.value = width;
    const div = document.getElementById("root") as HTMLDivElement;
    div.style.display = "none";
}
const handleEndGame = (pontuation) => {
    isRunning.value = false;
    endGame.value = true;
    score.value = pontuation;
    const div = document.getElementById("root") as HTMLDivElement;
    div.style.display = "block";
};
// onMounted(() => {
//     const { height, width } = document.body.getBoundingClientRect();

//     clientWidth.value = width;
// });
</script>
