<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import "flatpickr/dist/flatpickr.min.css";
import { ref, defineProps, onMounted, computed } from "vue";
import TextBox from "@/Components/TextBox.vue";
import CurrencyBox from "@/Components/CurrencyBox.vue";
import { format } from "date-fns";
import BaseInput from "@/Components/BaseInput.vue";
import BaseTable from "@/Components/BaseTable.vue";

const selectedTab = ref(1);
const movementColumns = [
  { label: "Data e hora", key: "date" },
  { label: "Fatura", key: "fat" },
  { label: "Valor", key: "value" },
];
const movementRows = [
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
  },
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
  },
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
  },
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
  },
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
  },
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
  },
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
  },
];
const paymentColumns = [...movementColumns, { label: "Status", key: "status" }];
const paymentRows = [
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
    status: "Pago",
  },
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
    status: "Pago",
  },
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
    status: "Pago",
  },
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
    status: "Pago",
  },
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
    status: "Pago",
  },
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
    status: "Pago",
  },
  {
    date: "10/10/2021 10:10",
    fat: "Fatura 1",
    value: "R$ 100,00",
    status: "Pago",
  },
];
const faturaColumns = [
  { label: "Data e hora", key: "date" },
  { label: "Valor da fatura", key: "fat" },
  { label: "Valor pago", key: "value" },
  { label: "Saldo", key: "saldo" },
];
const faturaRows = [
  {
    date: "10/10/2021 10:10",
    fat: "R$ 100,00",
    value: "R$ 100,00",
    saldo: "R$ 100,00",
  },
  {
    date: "10/10/2021 10:10",
    fat: "R$ 100,00",
    value: "R$ 100,00",
    saldo: "R$ 100,00",
  },
  {
    date: "10/10/2021 10:10",
    fat: "R$ 100,00",
    value: "R$ 100,00",
    saldo: "R$ 100,00",
  },
  {
    date: "10/10/2021 10:10",
    fat: "R$ 100,00",
    value: "R$ 100,00",
    saldo: "R$ 100,00",
  },
  {
    date: "10/10/2021 10:10",
    fat: "R$ 100,00",
    value: "R$ 100,00",
    saldo: "R$ 100,00",
  },
  {
    date: "10/10/2021 10:10",
    fat: "R$ 100,00",
    value: "R$ 100,00",
    saldo: "R$ 100,00",
  },
  {
    date: "10/10/2021 10:10",
    fat: "R$ 100,00",
    value: "R$ 100,00",
    saldo: "R$ 100,00",
  },
];
const columns = computed(() => {
  if (selectedTab.value === 1) return movementColumns;
  if (selectedTab.value === 2) return faturaColumns;
  if (selectedTab.value === 3) return paymentColumns;
});

const rows = computed(() => {
  if (selectedTab.value === 1) return movementRows;
  if (selectedTab.value === 2) return faturaRows;
  if (selectedTab.value === 3) return paymentRows;
});
function statusClass(status) {
  if (status === "Pago") return "text-green-500";
  if (status === "Pendente") return "text-yellow-500";
  else return "text-red-500";
}
const Ggr = import.meta.env.APP_GGR

</script>

<template>
  <Head title="Admin GGR" />

  <AuthenticatedLayout>
    <div v-if="Ggr">
      <div class="text-4xl text-white font-bold">GGR</div>
      <div class="flex mb-3">
        <div>
          <base-input
            label-style="font-bold text-xs text-white uppercase"
            label="Pagamento GGR"
            placeholder="Digite o valor a ser pago"
            class="flex-0"
          />
        </div>
        <div class="flex flex-col justify-end">
          <button
            class="bg-[#73FF61] text-black uppercase font-bold py-1 px-3 rounded-md ml-2"
          >
            Pagar Agora
          </button>
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-2">
        <CurrencyBox label="Saldo Atual GGR" :value="'200'" />
        <TextBox label="Porcentagem GGR" value="12%" />
        <CurrencyBox label="Total GGR Pago" :value="'200'" />
        <CurrencyBox label="Total GGR" :value="'200'" />
      </div>
      <BaseTable
        class="mt-6"
        hide-actions
        :columns="columns"
        :rows="rows"
        v-if="selectedTab === 1"
      >
      </BaseTable>
      <BaseTable
        class="mt-6"
        hide-actions
        :columns="columns"
        :rows="rows"
        v-if="selectedTab === 2"
      >
      </BaseTable>
      <BaseTable
        class="mt-6"
        hide-actions
        :columns="columns"
        :rows="rows"
        v-if="selectedTab === 3"
      >
        <template #status="{ value }">
          <td class="py-0" :class="statusClass(value)">
            {{ value }}
          </td>
        </template>
      </BaseTable>
      <div class="mb-3"></div>
      <div role="tablist" class="tabs tabs-bordered inline pt-10">
        <a
          role="tab"
          :class="selectedTab === 1 ? 'tab-active font-bold' : ''"
          class="text-2xl text-white tab h-12"
          @click="selectedTab = 1"
        >
          Movimentação Geral
        </a>
        <a
          role="tab"
          :class="selectedTab === 2 ? 'tab-active font-bold' : ''"
          class="text-2xl text-white tab h-12"
          @click="selectedTab = 2"
        >
          Faturas
        </a>
        <a
          role="tab"
          :class="selectedTab === 3 ? 'tab-active font-bold' : ''"
          class="text-2xl text-white tab h-12"
          @click="selectedTab = 3"
        >
          Pagamentos Realizados
        </a>
      </div>
    </div>
    <div v-else class="font-menu text-4xl text-center text-red-500 mt-[45vh]"> GGR desalibitado para essa conta.</div>
  </AuthenticatedLayout>
</template>
