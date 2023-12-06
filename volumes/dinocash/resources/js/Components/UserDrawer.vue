<template>
    <div class="border-8 border-black bg-white rounded-xl py-2 lg:pb-6 p-2">
        <div class="flex items-center mb-8 lg:ml-5">
            <img
                class="mr-1 lg:mr-3 rounded"
                width="50"
                height="50"
                :src="fotoPerfil"
            />
            <div>
                <div class="text-xs lg:text-sm text-gray-300">
                    Seja bem-vindo(a)
                </div>
                <div class="text-xl lg:text-2xl text-gray-700 -mt-3">
                    HIGORMUNIZ
                </div>
                <div class="tex-lg lg:text-xl text-gray-700">NÍVEL 100</div>
            </div>
        </div>

        <div class="gap-y-2 px-1 lg:px-6 flex flex-col text-white">
            <div class="drawer-button">
                <a>Saldo: {{ toBRL(wallet) }}</a>
            </div>
            <Link
                v-for="link in routes"
                class="drawer-button"
                :href="route(link.route)"
            >
                <a>{{ link.label }}</a>
            </Link>
        </div>
    </div>
</template>
<script setup lang="ts">
import fotoPerfil from "../../../storage/imgs/admin/fotodinoperfilpadrao.svg";
import { Link, usePage } from "@inertiajs/vue3";
import { defineProps } from "vue";

const routes = [
    {
        label: "Jogar",
        route: "user.play",
    },
    {
        label: "Histórico",
        route: "user.historico",
    },
    {
        label: "Movimentação",
        route: "user.movimentacao",
    },
    {
        label: "Depositar / Sacar",
        route: "user.deposito",
    },
    {
        label: "Alterar Senha",
        route: "user.alterar_senha",
    },
    {
        label: "Suporte",
        route: "user.suporte",
    },
];
const { wallet } = defineProps({
    wallet: {
        type: Number,
        default: 0,
    },
});

function toBRL(value) {
    return value.toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
}

// const page = usePage();

// const user = computed(() => page.props.auth.user.wallet);
// const wallet = toRef(user, "wallet");

// const echo = new Echo({
//   broadcaster: "pusher",
//   key: process.env.MIX_PUSHER_APP_KEY,
//   cluster: process.env.MIX_PUSHER_APP_CLUSTER,
// });

// onMounted(() => {
//   echo.channel("user." + auth.user.id).listen("WalletUpdated", (event) => {
//     wallet.value = event.user.wallet;
//   });
// });
</script>
