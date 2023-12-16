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
      <div v-if="!!$page.props.auth.user.isAffiliate" class="drawer-button">
        <a href="/afiliados" class="text-yellow-400 text-xl">Saldo: {{ toBRL(wallet) }}</a>
      </div>
      <div v-else class="drawer-button">
        <a class="text-yellow-400 text-xl">Saldo: {{ toBRL(wallet) }}</a>
      </div>
      <Link
        v-for="link in routes"
        class="drawer-button"
        :href="route(link.route)"
        @click="emit('close')"
      >
        <a>{{ link.label }}</a>
      </Link>
      <template v-if="$page.props.auth.user.role === 'admin'">
        <Link
          class="drawer-button"
          :href="route('admin.financeiro')"
          @click="emit('close')"
        >
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

const props = defineProps(["wallet"]);
const emit = defineEmits(["close"]);

const page = usePage();

const userId = computed(() => page.props.auth.user.id);
const userIdref = ref(userId);
const loading = ref(false);

const amount = ref(0);
const wallet = ref(props.wallet);

window.Echo.channel("wallet" + userIdref.value).listen("WalletChanged", (e) => {
  wallet.value = e.user.wallet;
});

const routes = [
  {
    label: "Depositar",
    route: "user.deposito",
  },
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
    label: "Sacar",
    route: "user.saque",
  },
];

const email = page.props.auth.user.name;

function toBRL(value) {
  return Number(value).toLocaleString("pt-br", {
    style: "currency",
    currency: "BRL",
  });
}
</script>
