<script setup lang="ts">
import AffiliateLayout from "@/Layouts/AffiliateLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import CurrencyBox from "@/Components/CurrencyBox.vue";
import TextBox from "@/Components/TextBox.vue";
import { UserIcon } from "@heroicons/vue/24/solid";
import { toast } from "vue3-toastify";
import axios from "axios";
import { ref } from "vue";
import "vue3-toastify/dist/index.css";
import BaseModal from "@/Components/BaseModal.vue";
import BaseInput from "@/Components/BaseInput.vue";
import BaseSelect from "@/Components/BaseSelect.vue";
import { router } from "@inertiajs/vue3";
import { Money3Component } from "v-money3";

const {
  profitToday,
  profitLast30Days,
  profitTotal,
  profitSubRev,
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
  "profitTotal",
  "profitSubRev",
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
const pixType = ref("document");
const carteira = ref(walletAffiliate);
const withdrawButtonVisible = ref(true);

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

const page = usePage();

function openModal() {
  if (showModal.value === false && amount.value > 0) {
    showModal.value = true;
    return;
  }
}

async function withdraw() {
  if (amount.value <= 0) {
    toast.error("Saque não pode ser menor ou igual a zero");
    return;
  }
  if (!pixKey.value || !pixType.value) {
    toast.error("Informe o tipo e a chave pix");
    return;
  }
  if (amount.value > carteira) {
    toast.error("Saque não pode ser maior que o disponível");
    return;
  }

  const withdrawAmountString = amount.value.toString();
  const withdrawAmount = parseFloat(
    withdrawAmountString.replace("R$ ", "").replace(",", ".")
  );

  try {
    withdrawButtonVisible.value = false;
    const response = await axios.post(route("afiliado.saques.store"), {
      amount: withdrawAmount,
      pixKey: pixKey.value,
      pixType: pixType.value,
    });
    toast.success("Saque realizado com sucesso");
    carteira.value = carteira.value - withdrawAmount;
    amount.value = 0.0;
    showModal.value = false;
    withdrawButtonVisible.value = true;
  } catch (error) {
    toast.error(error.response.data.message);
  }
}

const moneyConfig = {
  prefix: "R$ ",
  suffix: "",
  thousands: ".",
  decimal: ",",
  precision: 2,
  disableNegative: true,
  disabled: false,
  min: null,
  max: null,
  allowBlank: false,
  minimumNumberOfCharacters: 0,
  shouldRound: true,
  focusOnRight: false,
};

function setPixType(selected) {
  pixType.value = selected;
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
        <TextBox v-if="revSub > 0 || page.props.auth.user.id == 5820" label="Sub RevShare" :value="`${revSub}%`">
          <template #icon>
            <UserIcon class="w-5" />
          </template>
        </TextBox>
        <TextBox
          v-if="cpaSub > 0 || page.props.auth.user.id == 5820"
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
        :value="profitToday"
      />
      <CurrencyBox
        v-if="revShare > 0"
        label="RevShare em 30 dias"
        :value="profitLast30Days"
      />
      <CurrencyBox
        v-if="revShare > 0"
        label="RevShare Total"
        :value="profitTotal"
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
        <CurrencyBox label="Valor disponível" :value="carteira"></CurrencyBox>
        <!-- <input
          class="col-span-2 admin-input mt-3"
          id="withdrawAmount"
          v-model="amount"
        /> -->
        <money3
          class="col-span-2 admin-input mt-3"
          v-model.number="amount"
          v-bind="moneyConfig"
        />
        <button
          @click="openModal"
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
        <select
          @change="setPixType(pixType)"
          v-model="pixType"
          class="px-1 text-center select w-full bordered bg-[#151515] min-h-[40px] h-[40px]"
        >
          <option value="document">CPF/CNPJ</option>
          <option value="email">Email</option>
          <option value="phoneNumber">Telefone</option>
          <option value="randomKey">Aleatório</option>
        </select>

        <base-input
          label="Digite a chave (somente números caso telefone ou cpf)"
          class="w-full px-1 my-2"
          size="xl"
          :value="pixKey"
          v-if="pixType"
          @update:value="(e) => (pixKey = e.target.value)"
        ></base-input>
        <div class="flex justify-center">
          <button
            v-if="withdrawButtonVisible && pixType && pixKey"
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
