<template>
    <template v-if="logged">
        <div
            class="bg-roxo flex justify-between items-center h-16 px-1 sm:px-2 lg:grid lg:grid-cols-12"
        >
            <div
                v-if="logged"
                class="flex flex-1 lg:justify-normal lg:col-start-2 xl:col-start-3 items-center"
            >
                <bars3-icon
                    @click="emit('toggle')"
                    class="w-7 sm:w-8 sm:h-8 cursor-pointer fill-white lg:hidden"
                />
                <img class="h-5 sm:h-10 hidden lg:block" :src="DinoLogo" />
            </div>
            <div
                class="flex sm:flex-1 justify-end gap-x-1 sm:gap-x-2 lg:col-end-11 xl:col-end-12 pr-8"
            >
                <div class="dropdown">
                    <div
                        tabindex="0"
                        role="button"
                        class="bg-verde relative flex justify-center items-center rounded text-xs font-bold w-24 sm:text-md sm:w-32 h-10 text-roxo"
                    >
                        {{ userName }}
                        <ChevronDownIcon class="absolute right-1 top-3 w-4"
                            >aa</ChevronDownIcon
                        >
                    </div>
                    <ul
                        tabindex="0"
                        class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52"
                    >
                        <li><a>Bonus</a></li>
                        <!-- <li><a>Item 2</a></li> -->
                    </ul>
                </div>
                <div
                    @mouseover="bonusActive.value = true"
                    @mouseleave="bonusActive = false"
                    class="border border-verde text-xs sm:text-md text-verde p-1 sm:px-3 rounded flex justify-center items-center"
                >
                    {{ toBRL(money) }}
                </div>
                <Link class="" :href="route('logout')" method="post">
                    <img class="w-5 sm:w-7 mt-2" :src="leave" />
                </Link>
            </div>
        </div>
    </template>
    <div
        v-else
        class="bg-roxo flex justify-center items-center h-16 px-1 sm:px-2 py-2"
    >
        <div class="flex gap-x-3">
            <Link :href="route('login')">
                <div
                    class="user-button lg:text-[1rem] lg:py-1 items-center flex"
                >
                    Login
                </div>
            </Link>
            <Link :href="route('register')">
                <div
                    class="user-button lg:text-[1rem] lg:py-1 items-center flex"
                >
                    Registrar
                </div>
            </Link>
        </div>
    </div>
</template>
<script setup>
import { Link, usePage } from "@inertiajs/vue3";

import { Bars3Icon, ChevronDownIcon } from "@heroicons/vue/24/solid";
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
const bonusActive = ref(false);

const money = computed(() => {
    if (bonusActive.value) {
        return wallet + page.props.auth.user.bonusWallet;
    } else return wallet;
});
const userName = computed(() => {
    if (logged) {
        if (name) return name.split(" ")[0];
        else return "";
    } else return "";
});
</script>
