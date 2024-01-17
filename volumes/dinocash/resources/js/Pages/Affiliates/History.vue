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
const { affiliateHistory } = defineProps(["affiliateHistory"]);
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
  <Head title="Afiliado Comissões" />

  <AffiliateLayout>
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
                'bg-red-600': value === 'loss',
                'bg-green-600': value === 'win',
                'bg-green-800': value === 'CPA',
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
