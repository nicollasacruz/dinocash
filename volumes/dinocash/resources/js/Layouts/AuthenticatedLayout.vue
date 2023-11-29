<script setup lang="ts">
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { XMarkIcon, Bars3Icon } from "@heroicons/vue/24/solid";
import Background from "../../../storage/imgs/admin/bg.jpg";
import fotoPerfil from "../../../storage/imgs/admin/fotodinoperfilpadrao.svg";
import logoDino from "../../../storage/imgs/admin/logo dino branco painel.svg";
const routes = [
    {
        label: "Dashboard",
        route: "admin.dashboard",
    },
    {
        label: "Financeiro",
        route: "admin.financeiro",
    },
    {
        label: "Usuários",
        route: "admin.usuarios",
    },
    {
        label: "Afiliados",
        route: "admin.afiliados",
    },
    {
        label: "Saques",
        route: "admin.saque",
    },
    {
        label: "Depósitos",
        route: "admin.deposito",
    },
];
const test = ref(true);
</script>

<template>
    <div>
        <div class="h-screen text-montserrat flex">
            <div class="drawer col-auto lg:w-80 z-10 absolute lg:relative">
                <input
                    v-model="test"
                    id="my-drawer"
                    type="checkbox"
                    class="drawer-toggle"
                />

                <div class="drawer-side lg:w-80">
                    <label
                        for="my-drawer"
                        aria-label="close sidebar"
                        class="drawer-overlay"
                    ></label>
                    <ul
                        class="menu py-4 lg:w-80 min-h-full bg-[#212121] text-white relative"
                    >
                        <x-mark-icon
                            class="w-6 h-6 cursor-pointer absolute top-3 right-3 z-10 lg:hidden fill-white"
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
                class="drawer-content relative flex-1 px-4 py-2 lg:px-10 lg:py-8 overflow-auto"
                :style="{
                    'background-image': `url('${Background}')`,
                    'background-size': 'cover',
                    'background-position': 'center',
                }"
            >
                <!-- Page content here -->
                <bars3-icon
                    @click="test = !test"
                    class="w-6 h-6 absolute right-3 top-3 cursor-pointer lg:hidden block z-10 fill-white"
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
