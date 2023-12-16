<script setup lang="ts">
import { ref, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { XMarkIcon, Bars3Icon } from "@heroicons/vue/24/solid";
import Background from "../../../storage/imgs/bg-afiliado.png";
import fotoPerfil from "../../../storage/imgs/admin/fotodinoperfilpadrao.svg";
import logoDino from "../../../storage/imgs/admin/logo dino branco painel.svg";

const routes = [
    {
        label: "Dashboard",
        route: "afiliado.dashboard",
    },
    {
        label: "Saques",
        route: "afiliado.saques",
    },
    {
        label: "Comissões",
        route: "afiliado.historico",
    },
    {
        label: "Faturas",
        route: "afiliado.faturas",
    },
];
const drawer = ref(false);
window.addEventListener("resize", () => {
    if (window.innerWidth > 1024) {
        drawer.value = true;
    }
});
onMounted(() => {
    if (window.innerWidth > 1024) {
        drawer.value = true;
    }
});
function iOS() {
    return (
        [
            "iPad Simulator",
            "iPhone Simulator",
            "iPod Simulator",
            "iPad",
            "iPhone",
            "iPod",
        ].includes(navigator.platform) ||
        // iPad on iOS 13 detection
        (navigator.userAgent.includes("Mac") && "ontouchend" in document)
    );
}
onMounted(() => {
    Notification.requestPermission();
    // if (!iOS()) {
    // }
});

</script>

<template>
    <div>
        <div class="h-screen text-montserrat flex">
            <div class="drawer col-auto lg:w-80 z-10 absolute lg:relative">
                <input
                    v-model="drawer"
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
                        class="menu py-4 lg:w-80 min-h-full bg-[#2b0e36] text-white relative"
                    >
                        <x-mark-icon
                            class="w-6 h-6 cursor-pointer absolute top-3 right-3 z-10 lg:hidden fill-white"
                            @click="drawer = !drawer"
                        />
                        <Link :href="route('homepage')">
                            <img
                                class="mb-3 ml-5"
                                width="130"
                                :src="logoDino"
                                :href="route('homepage')"
                            />
                        </Link>
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
                                <div class="text-lg">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div
                                    class="text-red-400 underline cursor-pointer"
                                >
                                    <Link :href="route('logout')" method="post">
                                        Encerrar sessão
                                    </Link>
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
                    @click="drawer = !drawer"
                    class="w-6 h-6 absolute right-3 top-3 cursor-pointer lg:hidden block z-10 fill-white"
                />

                <slot />
            </div>
        </div>
    </div>
</template>
<style lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap");
.text-montserrat {
    font-family: "Montserrat", sans-serif !important;
}
.table-xs {
    tbody {
        tr {
            td {
                padding-top: 0px;
                padding-bottom: 0px;
            }
        }
    }
}
</style>
