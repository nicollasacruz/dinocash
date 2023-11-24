<script setup lang="ts">
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Head, Link } from "@inertiajs/vue3";
import { XMarkIcon, Bars3Icon } from "@heroicons/vue/24/solid";
import Background from "../../../storage/imgs/admin/bg.jpg";
import fotoPerfil from "../../../storage/imgs/admin/fotodinoperfilpadrao.svg";
import logoDino from "../../../storage/imgs/admin/logo dino branco painel.svg";
const showingNavigationDropdown = ref(false);
const routes = [
    {
        label: "Dashboard",
        route: "dashboard",
    },
    {
        label: "Financeiro",
        route: "finance",
    },
    {
        label: "Usuários",
        route: "users",
    },
    {
        label: "Afiliados",
        route: "affiliates",
    },
    {
        label: "Solicitações e saques",
        route: "requests",
    },
    {
        label: "Depósitos",
        route: "deposits",
    },
];
const test = ref(true);
</script>

<template>
    <div>
        <div class="h-screen text-montserrat flex">
            <div class="drawer col-auto md:w-80 z-10 absolute md:relative">
                <input
                    v-model="test"
                    id="my-drawer"
                    type="checkbox"
                    class="drawer-toggle"
                />

                <div class="drawer-side md:w-80">
                    <label
                        for="my-drawer"
                        aria-label="close sidebar"
                        class=""
                    ></label>
                    <ul
                        class="menu py-4 md:w-80 min-h-full bg-[#212121] text-white relative"
                    >
                        <x-mark-icon
                            class="w-6 h-6 cursor-pointer absolute top-3 right-3 z-10 md:hidden fill-white"
                            @click="test = !test"
                        />
                        <img class="mb-3 ml-5" width="130" :src="logoDino" />
                        <hr />
                        <div class="flex items-center mt-3 mb-8 ml-5 font-bold">
                            <img
                                class="mr-3"
                                width="50"
                                height="50"
                                :src="fotoPerfil"
                            />
                            <div>
                                <div class="">Seja bem-vindo(a)</div>
                                <div class="text-lg">Admin</div>
                                <div
                                    class="text-red-400 underline cursor-pointer"
                                >
                                    Encerrar sessão
                                </div>
                            </div>
                        </div>

                        <div class="divide-y-2 gap-y-4 px-6 divide-gray-500">
                            <div class="pt-3" v-for="link in routes">
                                <Link
                                    class="text-lg font-extrabold"
                                    :href="route(link.route)"
                                >
                                    <a>{{ link.label }}</a>
                                </Link>
                            </div>
                        </div>
                    </ul>
                </div>
            </div>
            <div
                class="drawer-content relative flex-1 px-10 py-8"
                :style="{
                    'background-image': `url('${Background}')`,
                    'background-size': 'cover',
                    'background-position': 'center',
                }"
            >
                <!-- Page content here -->
                <bars3-icon
                    @click="test = !test"
                    class="w-6 h-6 absolute right-3 top-3 cursor-pointer md:hidden block z-10 fill-white"
                />

                <slot />
            </div>
        </div>
    </div>
</template>
<style>
@import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap");
.text-montserrat {
    font-family: "Montserrat", sans-serif !important;
}
</style>
