<template>
    <Head title="Jogar" />

    <UserLayouyt v-slot="{ wallet }">
        <!-- <video id="videoId" class="hidden" controls autoplay muted>
      <source
        src="http://techslides.com/demos/sample-videos/small.mp4"
        type="video/mp4"
      />
    </video> -->
        <div class="p-2 lg:px-4 h-full">
            <div
                class="w-full h-full flex-col justify-center flex gap-y-4 text-gray-800"
            >
                <div class="text-center text-xl mb-1">
                    <p class="text-xl sm:text-2xl lg:text-4xl">Como Jogar:</p>
                    <p class="text-sm sm:text-lg lg:text-xl">
                        - Para iniciar o game aperte em qualquer lugar da tela!
                    </p>
                    <p class="text-sm sm:text-lg lg:text-xl">
                        - Para pular aperte com um dedo na tela e para abaixar,
                        pressione com dois dedos.
                    </p>
                    <p class="text-sm sm:text-lg lg:text-xl">
                        Para computadores:
                    </p>
                    <p class="text-sm sm:text-lg lg:text-xl">
                        - Para iniciar o game aperte a seta para cima ou a barra
                        de espaço!
                    </p>
                    <p class="text-sm sm:text-lg lg:text-xl">
                        - A setas para cima e para baixo direcionam o dino!
                    </p>
                </div>
                <div
                    class="text-center text-lg sm:text-xl font-bold mb-1 lg:hidden"
                >
                    Saldo disponível: {{ toBRL(wallet) }}
                </div>
                <input
                    type="text"
                    class="bg-white mx-auto max-w-xs border-8 rounded-xl border-gray-800 w-full"
                    placeholder="Digite o valor da aposta"
                    v-model="amount"
                    @input="formatAmount"
                />
                <div class="text-center">Aposta mínima: {{ toBRL($page.props.settings.minAmountPlay) }}</div>
                <div class="text-center">
                    Aposta máxima: {{ toBRL(maxAmmount) }}
                </div>
                <button
                    class="mx-auto py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                    @click="startGame"
                    :disabled="loading || !amount"
                >
                    <div v-if="loading">
                        <span class="loading loading-spinner loading-sm"></span>
                    </div>
                    <div v-else>Jogar</div>
                </button>
            </div>
            <GameCluster
                :amount="userId"
                v-if="isRunning"
                :viciosidade="viciosidade"
                :isAffiliate="isAffiliate"
                @end-game="handleEndGame"
                @finish-game="handleFinishGame"
                :active="isRunning"
                :height="clientHeight"
                :width="clientWidth"
            />
        </div>
        <BaseModal
            v-if="endGame || finishGame"
            :score="score"
            v-model="endGame"
        >
            <div v-if="endGame" class="text-center text-2xl">
                Você andou {{ score }} metros!
            </div>
            <div class="flex justify-center">
                <button
                    v-if="endGame"
                    class="mx-auto mt-5 py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                    @click="handleButtonClick()"
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
import GameCluster from "../../Components/GameCluster.vue";
import { computed, ref, toRef } from "vue";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const { viciosidade, isAffiliate, maxAmmount } = defineProps([
    "viciosidade",
    "isAffiliate",
    "maxAmmount",
]);
const finishGame = ref(false);
const page = usePage();

const user = computed(() => page.props.auth.user.id);
const userId = toRef(user, "userId");

const amount = ref(0);
const isRunning = ref(false);
const gameId = ref(0);
const endGame = ref(false);
const clientHeight = ref(0);
const clientWidth = ref(0);
const difficulty = ref(false);
const score = ref(0);
const type = ref("loss");
const loading = ref(false);
function handleButtonClick() {
    endGame.value = false;
    location.reload();
}

async function fetchStore() {
    try {
        if (amount.value < 1) {
            toast.error("Valor deve ser maior que R$1,00!");
            return;
        }
        if (amount.value > page.props.settings.maxAmountPlay) {
            toast.error(
                "Valor não pode ser maior que " +
                    toBRL(page.props.settings.maxAmountPlay)
            );
            return;
        }
        const response = await axios.post(route("user.play.store"), {
            amount: amount.value,
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

    const hashBuffer = await crypto.subtle.digest("SHA-256", data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray
        .map((byte) => byte.toString(16).padStart(2, "0"))
        .join("");

    return hashHex;
}

async function fetchUpdate() {
    try {
        const hash = await generateSHA256Hash(
            `${gameId.value}${userId.value}dinocash`
        );
        const response = await axios.patch(route("user.play.update"), {
            distance: score.value,
            gameId: gameId.value,
            type: type.value,
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
    loading.value = true;
    const lowPowerMode = await detectPowerSavingMode();
    console.log(lowPowerMode);
    if (lowPowerMode) {
        toast.error("Você está em modo de economia de energia!");
        return;
    }
    try {
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
    } catch (error) {
        console.error("Erro na pesquisa:", error);
        throw error;
    } finally {
        loading.value = false;
    }
}
function detectPowerSavingMode() {
    // for iOS/iPadOS Safari, and maybe MacBook macOS Safari (not tested)
    if (/(iP(?:hone|ad|od)|Mac OS X)/.test(navigator.userAgent)) {
        // In Low Power Mode, cumulative delay effect happens on setInterval()
        return new Promise((resolve) => {
            let fps = 60;
            let interval = 1000 / fps;
            let numFrames = 30;
            let startTime = performance.now();
            let i = 0;
            let handle = setInterval(() => {
                if (i < numFrames) {
                    i++;
                    return;
                }
                clearInterval(handle);
                let actualInterval =
                    (performance.now() - startTime) / numFrames;
                let ratio = actualInterval / interval; // 1.3x or more in Low Power Mode, 1.1x otherwise
                // alert(actualInterval+' '+interval);
                console.log(actualInterval, interval, ratio);
                resolve(ratio > 1.3);
            }, interval);
        });
    }
    // for Safari, Chromium, and maybe future Firefox
    return detectFrameRate().then((frameRate) => {
        // In Battery Saver Mode frameRate will be about 30fps or 20fps,
        // otherwise frameRate will be closed to monitor refresh rate (typically 60Hz)
        if (frameRate < 34) {
            return true;
        }
        // FIXME fallback to regard as Low Power Mode when battery power is low (down to 20%)
        else if (navigator.getBattery) {
            return navigator.getBattery().then((battery) => {
                return !battery.charging && battery.level <= 0.2 ? true : false;
            });
        }
        return undefined;
    });
}

function detectFrameRate() {
    return new Promise((resolve) => {
        let numFrames = 30;
        let startTime = performance.now();
        let i = 0;
        let tick = () => {
            if (i < numFrames) {
                i++;
                requestAnimationFrame(tick);
                return;
            }
            let frameRate =
                numFrames / ((performance.now() - startTime) / 1000);
            resolve(frameRate);
        };
        requestAnimationFrame(() => {
            tick();
        });
    });
}
const handleEndGame = (pontuation) => {
    isRunning.value = false;
    endGame.value = true;
    score.value = pontuation;
    type.value = "loss";
    const div = document.getElementById("root") as HTMLDivElement;
    div.style.display = "block";
    fetchUpdate();
};

const handleFinishGame = (pontuation) => {
    isRunning.value = false;
    endGame.value = true;
    score.value = pontuation;
    type.value = "win";
    const div = document.getElementById("root") as HTMLDivElement;
    div.style.display = "block";
    fetchUpdate();
};

function toBRL(value) {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
}

function formatAmount() {
    // Limpar caracteres não numéricos, exceto o ponto decimal
    let cleanedValue = amount.value.replace(/[^\d.]/g, "");

    // Permitir apenas um ponto decimal
    const decimalCount = cleanedValue.split(".").length - 1;
    if (decimalCount > 1) {
        cleanedValue = cleanedValue.slice(0, cleanedValue.lastIndexOf("."));
    }
    console.log(cleanedValue, "value");

    // Atualizar o valor
    amount.value = cleanedValue;
}
</script>
