<script setup lang="ts">
import AffiliateLayout from "@/Layouts/AffiliateLayout.vue";
import { Head } from "@inertiajs/vue3";
import CurrencyBox from "@/Components/CurrencyBox.vue";
import TextBox from "@/Components/TextBox.vue";
import { UserIcon } from "@heroicons/vue/24/solid";
import { toast } from "vue3-toastify";
import axios from "axios";
import { ref } from "vue";
import "vue3-toastify/dist/index.css";
import BaseModal from "@/Components/BaseModal.vue";
import BaseInput from "@/Components/BaseInput.vue";
import { router } from "@inertiajs/vue3";

const {
  profitToday,
  profitLast30Days,
  profitTotal,
  profitSubRev,
  lossToday,
  lossLast30Days,
  lossTotal,
  revShareTotal,
  profitCPAToday,
  profitCPALast30Days,
  profitCPATotal,
  profitSubCPATotal,
  countCPA,
  affiliateLink,
  walletAffiliate,
  revShare,
  CPA,
  cpaSub,
  revSub,
  paymentPending,
  countInvited,
} = defineProps([
  "profitToday",
  "profitLast30Days",
  "lossLast30Days",
  "profitTotal",
  "profitSubRev",
  "lossToday",
  "lossTotal",
  "revShareTotal",
  "profitCPAToday",
  "profitCPALast30Days",
  "profitCPATotal",
  "profitSubCPATotal",
  "countCPA",
  "affiliateLink",
  "walletAffiliate",
  "revShare",
  "CPA",
  "cpaSub",
  "revSub",
  "paymentPending",
  "countInvited",
]);
const showModal = ref(false);
const pixKey = ref("");
const pixType = ref("");

interface ImportMetaEnv {
  APP_URL: string;
}

axios.defaults.headers.common["X-CSRF-TOKEN"] = document.querySelector(
  'meta[name="csrf-token"]'
).content;
console.log("tokio", document.querySelector('meta[name="csrf-token"]').content);
const link = "https://dinocash.io/ref/" + affiliateLink;

const toBRL = (value) => {
  return Number(value).toLocaleString("pt-br", {
    style: "currency",
    currency: "BRL",
  });
};
function copy() {
  navigator.clipboard.writeText(link);
  toast.success("Copiado!");
}
const selectedUser = ref(null);
const amount = ref(0.0);

function permission() {
  document.dispatchEvent(
    new CustomEvent("notifyApple", {
      detail: Number(1),
    })
  );
}

async function withdraw() {
  if (showModal.value === false) {
    showModal.value = true;
    return;
  }
  if (amount.value <= 0) {
    toast.error("Saque não pode ser menor ou igual a zero");
    return;
  }
  if (!pixKey.value || !pixType.value) {
    toast.error("Informe o tipo e a chave pix");
    return;
  }
  if (amount.value > walletAffiliate) {
    toast.error("Saque não pode ser maior que o disponível");
    return;
  }

  console.log(pixKey, pixType, "pix");
  try {
    const response = await axios.post(route("afiliado.saques.store"), {
      amount: amount.value,
      pixKey: pixKey.value,
      pixType: pixType.value,
    });
    toast.success("Saque realizado com sucesso");
    walletAffiliate = walletAffiliate - amount.value
    amount.value = 0.0;
    pixType.value = "";
    pixKey.value = "";
    showModal.value = false;
  } catch (error) {
    toast.error("Não foi possivel realizar o saque");
  }
}

function formatAmount() {
  // Limpar caracteres não numéricos, exceto o ponto decimal
  let cleanedValue = amount.value.replace(/[^\d.]/g, "");

  // Permitir apenas um ponto decimal
  const decimalCount = cleanedValue.split(".").length - 1;
  if (decimalCount > 1) {
    cleanedValue = cleanedValue.slice(0, cleanedValue.lastIndexOf("."));
  }

  // Atualizar o valor
  amount.value = cleanedValue;
}
</script>

<template>
  <Head title="Afiliados Dashboard" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <AffiliateLayout>
    <div class="flex-row lg:flex xl:flex justify-between">
      <div class="text-4xl text-white font-bold mb-5">Dashboard</div>
      <!-- <div class="h-64">grafico</div> -->
      <div class="flex gap-x-5 -mt-4">
        <TextBox
          v-if="CPA > 0"
          label="CPA"
          :value="toBRL(CPA)"
          label-text="text-green-500"
        >
          <template #icon>
            <UserIcon class="w-5 fill-green-500" />
          </template>
        </TextBox>
        <TextBox v-if="revShare > 0" label="RevShare" :value="`${revShare}%`">
          <template #icon>
            <UserIcon class="w-5" />
          </template>
        </TextBox>
        <TextBox v-if="revSub > 0" label="Sub RevShare" :value="`${revSub}%`">
          <template #icon>
            <UserIcon class="w-5" />
          </template>
        </TextBox>
        <TextBox
          v-if="cpaSub > 0"
          label="Sub CPA"
          :value="toBRL(cpaSub)"
          label-text="text-green-500"
        >
          <template #icon>
            <UserIcon class="w-5 fill-green-500" />
          </template>
        </TextBox>
      </div>
    </div>

    <div
      class="grid xs: grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-x-2 gap-y-2 mt-4"
    >
      <CurrencyBox
        v-if="revShare > 0"
        label="RevShare do dia"
        :value="profitToday - lossToday"
      />
      <CurrencyBox
        v-if="revShare > 0"
        label="RevShare em 30 dias"
        :value="profitLast30Days - lossLast30Days"
      />
      <CurrencyBox
        v-if="revShare > 0"
        label="RevShare Total"
        :value="profitTotal - lossTotal"
      />
      <CurrencyBox
        v-if="revSub > 0"
        label="Sub RevShare Total"
        :value="profitSubRev"
      />
      <CurrencyBox
        v-if="CPA > 0"
        label="Lucro CPA do dia"
        :value="profitCPAToday"
      />
      <CurrencyBox
        v-if="CPA > 0"
        label="Lucro CPA 30 dias"
        :value="profitCPALast30Days"
      />
      <CurrencyBox
        v-if="CPA > 0"
        label="Lucro CPA Total"
        :value="profitCPATotal"
      />
      <CurrencyBox
        v-if="cpaSub > 0"
        label="Lucro Sub CPA Total"
        :value="profitSubCPATotal"
      />
      <TextBox label="Convidados" :value="countInvited" />
      <TextBox v-if="CPA > 0" label="Convidados CPA" :value="countCPA" />
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-x-3 mt-10">
      <TextBox
        label="Link de afiliado"
        label-text="text-2xl text-white !text-left capitalize"
        value-text="!text-left flex items-center text-xs lg:text-xl"
        class="lg:col-span-2"
        :value="link"
      >
        <template #action>
          <button
            @click="copy()"
            class="btn min-h-[2rem] h-[2rem] bg-yellow-500 text-black hover:text-white"
          >
            Copiar
          </button>
        </template>
      </TextBox>
      <div class="grid grid-cols-2 gap-x-2 col-span-2 mt-4">
        <CurrencyBox
          label="Valor pendente"
          :value="paymentPending"
        ></CurrencyBox>
        <CurrencyBox
          label="Valor disponível"
          :value="walletAffiliate"
        ></CurrencyBox>
        <input
          class="col-span-2 admin-input mt-3"
          v-model="amount"
          @input="formatAmount"
        />
        <button
          @click="withdraw"
          class="btn bg-yellow-500 text-black hover:text-white col-span-2 mt-1 uppercase"
        >
          Solicitar saque de comissões
        </button>
        <button
          @click="permission"
          class="btn bg-yellow-500 text-black hover:text-white col-span-2 mt-1 uppercase"
        >
          Permitir notificações
        </button>
      </div>
    </div>
    <BaseModal
      title="Informações do saque"
      v-model="showModal"
      @close="showModal = false"
      v-if="showModal"
    >
      <div>
        <base-input
          class="px-1"
          label="Tipo chave pix"
          type="text"
          :options="[
            { value: 'document', label: 'CPF/CNPJ' },
            { value: 'email', label: 'Email' },
            { value: 'phoneNumber', label: 'Telefone' },
            { value: 'randomKey', label: 'Aleatório' },
          ]"
          v-model="pixType"
          @update:value="(e) => (pixType = e.target.value)"
        />
        <base-input
          label="Digite a chave (somente números caso telefone ou cpf)"
          class="w-full px-1 my-2"
          size="xl"
          :value="pixKey"
          @update:value="(e) => (pixKey = e.target.value)"
        ></base-input>
        <div class="flex justify-center">
          <button
            class="bg-verde-claro text-black font-menu px-6 py-3 rounded-lg mt-4"
            @click="withdraw"
          >
            SACAR
          </button>
        </div>
      </div>
    </BaseModal>
  </AffiliateLayout>
</template>
