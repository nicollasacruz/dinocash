<script setup lang="ts">
import AffiliateLayout from "@/Layouts/AffiliateLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { ref, defineProps } from "vue";
import TextBox from "@/Components/TextBox.vue";
import dayjs from "dayjs";
const columns = [
  { label: "Data", key: "created_at" },
  { label: "Valor", key: "amount" },
  { label: "Status", key: "type" },
  { label: "Pago Em", key: "approvedAt" },
];
const { affiliatesWithdrawsList, user } = defineProps(["affiliatesWithdrawsList", 'user']);

const showModal = ref(false);

const toBRL = (value) => {
  return Number(value).toLocaleString("pt-br", {
    style: "currency",
    currency: "BRL",
  });
};
const getStatus = (status) => {
  switch (status) {
    case "paid":
      return "PAGO";
    case "rejected":
      return "REJEITADO";
    default:
      return "PENDENTE";
  }
};
const rows = [];
</script>

<template>
  <Head title="Afiliado Saques" />

  <AffiliateLayout>
    <div class="text-4xl text-white font-bold mb-3">Saques</div>
    <div class="my-3 flex justify-between">
      <div>
        <div class="font-bold text-white uppercase mb-1">
          Pesquisar saques
        </div>
        <input
          type="text"
          class="admin-input"
          placeholder="Digite o email do usuário... "
        />
      </div>
      <!-- <div class="flex gap-x-5">
        <div class="flex gap-x-5">
          <TextBox
            label="CAIXA DA CASA"
            :value="toBRL(2)"
            value-text="text-center text-green-500"
          />
          <TextBox
            label="total de depósitos hoje"
            :value="toBRL(2)"
            value-text="text-center text-green-500"
          />
        </div>
      </div> -->
    </div>
    <BaseTable
      hide-actions
      :columns="columns"
      :rows="affiliatesWithdrawsList"
      class="table-xl mt-6 h-3/4"
    >
      <template #created_at="{ value }">
        <td class="py-1">
          {{ dayjs(value).format("DD/MM/YYYY HH:mm:ss") }}
        </td>
      </template>
      <template #approvedAt="{ value }">
        <td class="py-1">
          {{ value ? dayjs(value).format("DD/MM/YYYY HH:mm:ss") : "-" }}
        </td>
      </template>
      <template #amount="{ value }">
        <td class="py-1">
          {{
            Number(value).toLocaleString("pt-br", {
              style: "currency",
              currency: "BRL",
            })
          }}
        </td>
      </template>
      <template #type="{ value }">
        <td class="py-1">
          <div class="no-wrap text-xs cursor-pointer">
            <div
              class="badge w-24 rounded-sm border-0 text-xs font-bold text-white"
              :class="{
                'bg-gray-600': value === 'pending',
                'bg-red-600': value === 'rejected',
                'bg-green-600': value === 'paid',
              }"
            >
              {{ getStatus(value) }}
            </div>
          </div>
        </td>
      </template>
    </BaseTable>
    <BaseModal v-model="showModal"> teste </BaseModal>
  </AffiliateLayout>
</template>
