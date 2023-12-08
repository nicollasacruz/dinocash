<template>
  <UserLayouyt>
    <div class="p-2 lg:px-8">
      <div class="text-center uppercase text-xl lg:text-3xl text-gray-800 mb-4">
        Sacar
      </div>
      <div class="w-full text-center flex-col flex gap-y-4 text-gray-800">
        <input
          type="number"
          class="bg-white mx-auto max-w-xs border-8 rounded-xl border-gray-800 w-full"
          placeholder="Digite o valor da aposta"
          v-model="amount"
          :min="minWithdraw"
          :max="maxWithdraw"
        />
        <img :src="pixLogo" class="mx-auto mb-5 w-32 max-w-sm" alt="" />
        <button
          @click="withdraw"
          class="mx-auto py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
        >
          Sacar
        </button>
        <div class="mt-10 text-lg">
          <div>Saldo disponível:</div>
          <div>{{ toBRL(wallet) }}</div>
        </div>
        <div class="mt-10">
          Saques serão enviados em até 24 horas úteis após a retirada.
        </div>
      </div>
    </div>
    <Loading :loading="loading" />
  </UserLayouyt>
</template>

<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import dayjs from "dayjs";
import { computed, ref } from "vue";
import pixLogo from "../../../../storage/imgs/user/pix_logo.svg";
import axios from "axios";
import Loading from "../../Components/Loading.vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { number } from "yup";
import { usePage } from "@inertiajs/vue3";

const { minWithdraw, maxWithdraw, walletUser } = defineProps([
  "minWithdraw",
  "maxWithdraw",
  "walletUser",
]);

const page = usePage();

const userId = computed(() => page.props.auth.user.id);
const userIdref = ref(userId);
const loading = ref(false);

const amount = ref(0);
const wallet = ref(walletUser);

window.Echo.channel("wallet" + userIdref.value).listen("WalletChanged", (e) => {
  console.log(e.user.wallet, "withdraw");
  wallet.value = e.user.wallet;
});

async function withdraw() {
  try {
    const valor = amount.value;
    const wallet = walletUser;
    if (valor < minWithdraw) {
      toast.error("Valor mínimo permitido é : " + toBRL(minWithdraw));
      return;
    }
    if (valor > maxWithdraw) {
      toast.error("Valor maximo permitido é : " + toBRL(maxWithdraw));
      return;
    }
    if (valor > wallet) {
      toast.error("Saldo indsponivel.");
      return;
    }
    const { data } = await axios.post(route("user.saque.store"), {
      amount: amount.value,
    });
    console.log(data);
    if (data.status === "error") {
      toast.error(data.message);
      return;
    }
    toast.success(data.message);
  } catch (error) {
    alert("Erro na solicitação");
  } finally {
    amount.value = 0.0;
  }
}

function toBRL(value) {
  value = Number.parseFloat(value);
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(value);
}
</script>
