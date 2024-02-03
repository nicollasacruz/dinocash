<template>
    <div class="rounded-xl p-2 lg:w-72 bg-[#1c1320]">
        <div class="flex flex-col items-center mb-3 lg:ml-5">
            <div class="relative">
                <img
                    class="mr-1 lg:mr-3 rounded mt-2"
                    width="120"
                    height="120"
                    :src="fotoPerfil"
                />
                <!-- <Link
                    :href="route('user.alterar_icone')"
                    class="absolute top-0 right-0 bg-verde-escuro p-[1px] rounded-sm cursor-pointer"
                >
                    <PencilSquareIcon class="w-5 fill-black" />
                </Link> -->
            </div>
            <div class="text-xs lg:text-xs text-gray-400 mt-2">
                Seja bem-vindo(a)
            </div>
            <div
                class="text-xl flex items-center lg:text-2xl text-verde font-bold capitalize"
            >
                <div>
                    {{ email?.split(" ")[0] }}
                </div>
                <Link class="" :href="route('logout')" method="post">
                    <img class="w-5 ml-3" :src="leave" />
                </Link>
            </div>
        </div>

        <div class="gap-y-2 px-1 lg:px-2 flex flex-col text-white font-bold">
            <Link
                v-for="link in routes"
                class="drawer-button flex items-center pl-5"
                :href="route(link.route)"
                @click="emit('close')"
            >
                <img class="mr-2" :src="link.icon" />
                <a>{{ link.label }}</a>
            </Link>
            <template v-if="$page.props.auth.user?.role === 'admin'">
                <Link
                    class="drawer-button flex"
                    :href="route('admin.financeiro')"
                    @click="emit('close')"
                >
                    <img class="mr-2" :src="admin" />

                    <a>Admin</a>
                </Link>
            </template>
        </div>
    </div>
</template>

<script setup lang="ts">
import fotoPerfil from "../../../storage/imgs/admin/fotodinoperfilpadrao.svg";
import { Link, usePage } from "@inertiajs/vue3";
import { defineEmits, defineProps, computed, ref, toRef } from "vue";
import { PencilSquareIcon } from "@heroicons/vue/24/solid";
import cart from "../../../storage/imgs/user/icons/cart.svg";
import admin from "../../../storage/imgs/user/icons/admin.svg";
import history from "../../../storage/imgs/user/icons/history.svg";
import money from "../../../storage/imgs/user/icons/money.svg";
import monitor from "../../../storage/imgs/user/icons/monitor.svg";
import person from "../../../storage/imgs/user/icons/person.svg";
import deposit from "../../../storage/imgs/user/icons/deposit.svg";
import leave from "../../../storage/imgs/user/icons/leave.svg";

const props = defineProps(["wallet"]);
const emit = defineEmits(["close"]);

const page = usePage();

const userId = computed(() => page.props.auth.user?.id);
const userIdref = ref(userId);
const loading = ref(false);

const amount = ref(0);
const wallet = ref(props.wallet + page.props.auth.user?.bonusWallet);

window.Echo.channel("wallet" + userIdref.value).listen("WalletChanged", (e) => {
    wallet.value = e.message.wallet;
});

const routes = [
    {
        label: "Depositar",
        route: "user.deposito",
        icon: deposit,
    },
    {
        label: "Perfil",
        route: "user.edit",
        icon: person,
    },
    {
        label: "Jogar",
        route: "user.play",
        icon: person,
    },
    {
        label: "Histórico",
        route: "user.historico",
        icon: history,
    },
    {
        label: "Movimentações",
        route: "user.movimentacao",
        icon: monitor,
    },
    // {
    //     label: "Loja",
    //     route: "user.shop",
    //     icon: cart,
    // },
    {
        label: "Sacar",
        route: "user.saque",
        icon: money,
    },
    // {
    //     label: "Admin",
    //     route: "user.alterar_icone",
    //     icon: admin,
    // },
];

const email = page.props.auth.user?.name;

function toBRL(value) {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
}
</script>
