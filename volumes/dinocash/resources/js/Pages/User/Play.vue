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
import { computed, onMounted, ref, toRef } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import axios from "axios";

const page = usePage();

const user = computed(() => page.props.auth.user.id);
const userId = toRef(user, "userId");
const props = computed(() => page.props);
const propsList = toRef(props, "propsList");

const isRunning = ref(false);
const gameId = ref(0);
const endGame = ref(false);
const clientHeight = ref(0);
const clientWidth = ref(0);
const difficulty = ref(false);
const score = ref(0);

async function fetchStore() {
  try {
    const response = await axios.post(route("user.play.store"), {
      amount: 10,
    });

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

  const hashBuffer = await crypto.subtle.digest('SHA-256', data);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  const hashHex = hashArray.map(byte => byte.toString(16).padStart(2, '0')).join('');
  console.log(hashHex, 'hash value');
  return hashHex;
}


async function fetchUpdate() {
  try {
    console.log(`${gameId.value}${userId.value}dinocash`, 'value');
    const hash = await generateSHA256Hash(`${gameId.value}${userId.value}dinocash`)
    const response = await axios.patch(route("user.play.update"), {
      distance: score.value,
      gameId: gameId.value,
      type: 'loss',
      token: hash,
    });

    const result = response.data;

    return result;

  } catch (error) {
    console.error("Erro na pesquisa:", error);

    throw error;
  }
}

async function startGame() {
  gameId.value = await fetchStore();
  const { height, width } = document.body.getBoundingClientRect();
  isRunning.value = true;
  clientHeight.value = height;
  clientWidth.value = width;
  difficulty.value = true;
  const div = document.getElementById("root") as HTMLDivElement;
  div.style.display = "none";
}
const handleEndGame = (pontuation) => {
    isRunning.value = false;
    endGame.value = true;
    score.value = pontuation;
    const div = document.getElementById("root") as HTMLDivElement;
    div.style.display = "block";
    fetchUpdate();
};
// onMounted(() => {
//     const { height, width } = document.body.getBoundingClientRect();

//     clientWidth.value = width;
// });
</script>
