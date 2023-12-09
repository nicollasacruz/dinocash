<script setup lang="ts">
import AffiliateLayout from "@/Layouts/AffiliateLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import CurrencyBox from "@/Components/CurrencyBox.vue";
import TextBox from "@/Components/TextBox.vue";
import { UserIcon } from "@heroicons/vue/24/solid";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const showModal = ref(false);
const {
  profitToday,
  profitLast30Days,
  profitTotal,
  revShareTotal,
  profitCPA,
  countCPA,
  affiliateLink,
  walletAffiliate,
  revShare,
  CPA,
  paymentPending,
} = defineProps([
  "profitToday",
  "profitLast30Days",
  "profitTotal",
  "revShareTotal",
  "profitCPA",
  "countCPA",
  "affiliateLink",
  "walletAffiliate",
  "revShare",
  "CPA",
  "paymentPending",
]);

interface ImportMetaEnv {
  APP_URL: string;
}

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
</script>

<template>
  <Head title="Afiliados Dashboard" />

  <AffiliateLayout>
    <div class="flex justify-between">
      <div class="text-4xl text-white font-bold mb-5">Dashboard</div>
      <div class="h-64">grafico</div>
      <div class="flex gap-x-5 -mt-4">
        <TextBox label="CPA" :value="toBRL(CPA)" label-text="text-green-500">
          <template #icon>
            <UserIcon class="w-5 fill-green-500" />
          </template>
        </TextBox>
        <TextBox label="RevShare" :value="`${revShare}%`">
          <template #icon>
            <UserIcon class="w-5" />
          </template>
        </TextBox>
      </div>
    </div>

    <div
      class="grid xs: grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-x-2 mt-4"
    >
      <CurrencyBox label="Lucro em 30 dias" :value="2" />
      <CurrencyBox label="Prejuizo em 30 dias" :value="2" negative />
      <CurrencyBox label="Lucro Total" :value="2" />
      <CurrencyBox label="Prejuizo Total" :value="2" negative />
      <CurrencyBox label="Lucro do dia" :value="2" />
    </div>
    <div class="grid grid-cols-5 gap-x-3 mt-10">
      <TextBox
        label="Link de afiliado"
        label-text="text-2xl text-white !text-left capitalize"
        value-text="!text-left flex items-center"
        class="col-span-3"
        :value="link"
      >
        <template #action>
          <button
            @click="copy()"
            class="btn min-h-[2rem] h-[2rem] bg-yellow-500 text-black hover:text-white"
          >
            Copiar o link
          </button>
        </template>
      </TextBox>
      <div class="grid grid-cols-2 gap-x-2 col-span-2 mt-4">
        <CurrencyBox label="Valor pendente" value="200"></CurrencyBox>
        <CurrencyBox label="Valor disponível" value="200"></CurrencyBox>
        <button
          class="btn bg-yellow-500 text-black hover:text-white col-span-2 mt-1 uppercase"
        >
          Solicitar saque de comissões
        </button>
      </div>
    </div>
  </AffiliateLayout>
</template>
