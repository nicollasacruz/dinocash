<template>
    <template v-if="logged">
        <div
            class="bg-[#150822] flex justify-between items-center min-h-[64px] py-2 lg:min-h-[80px] px-1 sm:px-2 lg:grid lg:grid-cols-12"
        >
            <div
                v-if="logged"
                class="flex flex-1 lg:justify-normal lg:col-start-2 xl:col-start-3 items-center"
            >
                <bars3-icon
                    @click="emit('toggle')"
                    class="w-6 min-w-[20px] sm:w-8 sm:h-8 cursor-pointer fill-white lg:hidden ml-1 lg:ml-3 mr-3"
                />
                <Link href="/">
                    <img class="h-5 logo sm:h-10 lg:block" :src="DinoLogo" />
                </Link>
            </div>
            <div
                class="flex sm:flex-1 items-center justify-end gap-x-1 sm:gap-x-2 lg:col-end-11 xl:col-end-12 pr-3"
            >
                <div
                    class="bg-verde relative lg:flex justify-center items-center rounded font-bold sm:text-md hidden h-9 text-roxo"
                >
                    <div class="w-32 text-center">
                        {{ userName }}
                    </div>
                </div>

                <Link
                    class="font-bold ml-2 mr-1 py-1 px-3 lg:mr-0 lg:ml-0 text-xs lg:text-base text-red-700 rounded-md border border-red-700"
                    :href="route('user.deposito')"
                >
                    <div>DEPOSITAR</div>
                </Link>

                <div
                    @pointerdown="bonusActive = false"
                    @pointerup="bonusActive = true"
                    @mouseover="bonusActive = false"
                    @mouseleave="bonusActive = true"
                    class="text-xs sm:text-sm font-bold text-verde p-1 px-3 sm:px-3 lg:pr-6 rounded-full border-2 border-verde flex justify-center items-center"
                >
                    <span class="select-none">{{ toBRL(money) }}</span>

                    <WalletIcon class="w-4 sm:w-4 -mr-2 lg:-mr-5 fill-white" />
                </div>
                <Link class="" :href="route('logout')" method="post">
                    <img class="w-5 sm:w-7 lg:min-w-[30px] ml-2" :src="leave" />
                </Link>
            </div>
        </div>
    </template>
    <div
        v-else
        class="bg-roxo flex justify-center items-center h-20 py-6 px-1 sm:px-2 py-2"
    >
        <div class="flex gap-x-3">
            <Link :href="route('login')">
                <div
                    class="user-button py-2 px-5 lg:px-10 lg:text-base lg:py-3 items-center flex"
                >
                    Login
                </div>
            </Link>
            <Link :href="route('register')">
                <div
                    class="user-button py-2 px-5 lg:px-10 lg:text-base lg:py-3 items-center flex"
                >
                    Registrar
                </div>
            </Link>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3";

import {
    Bars3Icon,
    ChevronDownIcon,
    WalletIcon,
} from "@heroicons/vue/24/solid";
import DinoLogo from "../../../storage/imgs/home-page/dino-logo.svg";
import leave from "../../../storage/imgs/user/icons/leave.svg";
import { computed } from "vue";
import { ref } from "vue";

const emit = defineEmits("toggle");
const { wallet, name, logged } = defineProps(["wallet", "name", "logged"]);
function toBRL(value) {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
}
const page = usePage();
const bonusActive = ref(true);

const walletTotal = ref(logged ? wallet * 1 : 0);
const bonusTotal = ref(logged ? page.props.auth.user.bonusWallet * 1 : 0);
const money = computed(() => {
    if (bonusActive.value) {
        return walletTotal + bonusTotal;
    } else return walletTotal;
});


if (logged) {
window.Echo.channel("wallet" + logged ? page.props.auth.user.id : 0).listen("WalletChanged", (e) => {
    walletTotal.value = e.message.wallet;
    bonusTotal.value = e.message.bonus;
});
}

const userName = computed(() => {
    if (logged) {
        if (name) return name.split(" ")[0];
        else return "";
    } else return "";
});
</script>
<style>
.logo {
    @media (min-width: 380px) {
        height: 1.8rem;
    }
}
</style>
