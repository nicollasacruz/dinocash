<template>
    <div class="bg-roxo flex justify-between items-center h-16 px-1 sm:px-2">
        <div class="flex flex-1 lg:justify-center items-center">
            <bars3-icon
                @click="emit('toggle')"
                class="w-7 sm:w-8 sm:h-8 cursor-pointer fill-white lg:hidden"
            />
            <img class="h-5 sm:h-10" :src="DinoLogo" />
        </div>
        <div class="flex sm:flex-1 justify-end gap-x-1 sm:gap-x-2">
            <div class="dropdown">
                <div
                    tabindex="0"
                    role="button"
                    class="bg-verde-escuro relative flex justify-center items-center rounded text-xs w-24 sm:text-md sm:w-32 h-10 text-roxo"
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
                class="border border-verde-escuro text-xs sm:text-md text-verde-escuro p-1 sm:px-3 rounded flex justify-center items-center"
            >
                {{ toBRL(wallet) }}
            </div>
            <Link class="" :href="route('logout')" method="post">
                <img class="w-5 sm:w-7 mt-2" :src="leave" />
            </Link>
        </div>
    </div>
</template>
<script setup>
import { Link, usePage } from "@inertiajs/vue3";

import { Bars3Icon, ChevronDownIcon } from "@heroicons/vue/24/solid";
import DinoLogo from "../../../storage/imgs/home-page/dino-logo.svg";
import leave from "../../../storage/imgs/user/icons/leave.svg";
const emit = defineEmits("toggle");
const { wallet, name } = defineProps(["wallet", "name"]);
function toBRL(value) {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
}
const [userName] = name.split(" ");
</script>
