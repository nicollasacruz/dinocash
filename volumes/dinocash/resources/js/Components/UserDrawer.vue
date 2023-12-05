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
                <a>Saldo: R$ 10.000,00</a>
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
import { Link } from "@inertiajs/vue3";

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
</script>
