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
        <div class="text-xs lg:text-sm text-gray-400">Seja bem-vindo(a)</div>
        <div class="text-xl lg:text-2xl text-gray-700 -mt-2">{{ email }}</div>
        <!-- <div class="tex-lg lg:text-xl text-gray-700">NÍVEL 100</div> -->
        <Link
          class="tex-lg lg:text-sm text-red-700"
          :href="route('logout')"
          method="post"
        >
          Encerrar sessão
        </Link>
      </div>
    </div>

    <div class="gap-y-2 px-1 lg:px-6 flex flex-col text-white">
      <div class="drawer-button">
        <a>Saldo: {{ toBRL(props.wallet) }}</a>
      </div>
      <Link
        v-for="link in routes"
        class="drawer-button"
        :href="route(link.route)"
        @click="emit('close')"
      >
        <a>{{ link.label }}</a>
      </Link>
    </div>
  </div>
</template>

<script setup lang="ts">
import fotoPerfil from "../../../storage/imgs/admin/fotodinoperfilpadrao.svg";
import { Link, usePage } from "@inertiajs/vue3";
import { defineEmits, defineProps, computed, ref, toRef } from "vue";
const props = defineProps(['wallet'])
const emit = defineEmits(["close"]);
const routes = [
  {
    label: "perfil",
    route: "user.edit",
  },
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
    label: "Depositar",
    route: "user.deposito",
  },
  {
    label: "Sacar",
    route: "user.saque",
  },
  // {
  //   label: "Suporte",
  //   route: "user.suporte",
  // },
];

const page = usePage();

const email = page.props.auth.user.email;

function toBRL(value) {
  return Number(value).toLocaleString("pt-br", {
    style: "currency",
    currency: "BRL",
  });
}
</script>
