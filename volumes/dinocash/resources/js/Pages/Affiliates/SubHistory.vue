<script setup lang="ts">
import AffiliateLayout from "@/Layouts/AffiliateLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import { defineProps } from "vue";
import TextBox from "@/Components/TextBox.vue";
import dayjs from "dayjs";
import Paginator from "@/Components/Paginator.vue";

const columns = [
  { label: "Data", key: "updated_at" },
  { label: "Valor", key: "amount" },
  { label: "Status", key: "type" },
  { label: "Nº Fatura", key: "affiliateInvoiceId" },
  { label: "Faturado Em", key: "invoicedAt" },
];
const { affiliateHistory, topSubAffiliatesCPA } = defineProps([
  "affiliateHistory",
  "topSubAffiliatesCPA",
]);
const toBRL = (value) => {
  return Number(value).toLocaleString("pt-br", {
    style: "currency",
    currency: "BRL",
  });
};
const getStatus = (status) => {
  switch (status) {
    case "revSub":
      return "GANHO SUB";
    case "cpaSub":
      return "CPA SUB";
    case "win":
      return "GANHO";
    case "loss":
      return "PERDA";
    default:
      return "CPA";
  }
};
const rows = [];
</script>

<template>
  <Head title="SubAfiliado Comissões" />

  <AffiliateLayout>
    <div class="box p-2 mt-2">
      <div class="text-green-500 text-center font-bold text-xs uppercase mb-2">
        Afiliados que mais trouxeram cadastros
      </div>
      <div class="grid-cols-2">
        <div
          class="text-xs pt-1"
          v-for="{ email, totalCount } in topSubAffiliatesCPA"
        >
          <div class="text-white">
            {{ email }} - {{ totalCount }} convidados
          </div>
        </div>
      </div>
    </div>
    <div class="text-4xl text-white font-bold mb-3">
      Histórico de pagamentos
    </div>
    <BaseTable
      hide-actions
      :columns="columns"
      :rows="affiliateHistory.data"
      class="table-xs mt-6 h-3/4"
    >
      <template #updated_at="{ value }">
        <td>
          {{ dayjs(value).format("DD/MM/YYYY HH:mm:ss") }}
        </td>
      </template>
      <template #invoicedAt="{ value }">
        <td>
          {{ value ? dayjs(value).format("DD/MM/YYYY HH:mm:ss") : null }}
        </td>
      </template>
      <template #amount="{ value }">
        <td>
          {{
            Number(value).toLocaleString("pt-br", {
              style: "currency",
              currency: "BRL",
            })
          }}
        </td>
      </template>
      <template #type="{ value }">
        <td>
          <div class="no-wrap text-xs cursor-pointer">
            <div
              class="badge w-24 rounded-sm border-0 text-xs font-bold text-white"
              :class="{
                'bg-green-600': value === 'revSub',
                'bg-green-800': value === 'cpaSub',
              }"
            >
              {{ getStatus(value) }}
            </div>
          </div>
        </td>
      </template>
    </BaseTable>
    <Paginator :data="affiliateHistory" class="mt-4" />
  </AffiliateLayout>
</template>
