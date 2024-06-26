<template>
  <BaseTable
    hideActions
    class="mt-7 table-xs h-3/4"
    :columns="columns"
    :rows="rows"
  >
    <template #type="{ value, item }">
      <td>
        <div class="no-wrap text-xs cursor-pointer">
          <div v-if="value === 'pending'" class="flex gap-x-2">
            <div
              :class="{ disabled: isButtonDisabled }"
              @click="pay(item.id)"
              class="badge w-24 font-bold rounded-sm badge-success no-wrap text-black whitespace-nowrap text-xs cursor-pointer"
            >
              PAGAR
            </div>
            <div
              :class="{ disabled: isButtonDisabled }"
              @click="reject(item.id)"
              class="badge w-24 font-bold rounded-sm bg-red-600 border-0 no-wrap text-black whitespace-nowrap text-xs cursor-pointer"
            >
              RECUSAR
            </div>
          </div>
          <div
            v-else
            class="badge w-24 rounded-sm border-0 text-xs font-bold text-white"
            :class="{
              'bg-red-600': value === 'rejected',
              'bg-green-600': value === 'paid',
            }"
          >
            {{ getStatus(value) }}
          </div>
        </div>
      </td>
    </template>
    <template #updated_at="{ value }">
      <td>
        {{ dayjs(value).format("DD/MM/YYYY HH:mm:ss") }}
      </td>
    </template>
    <template #amount="{ value }">
      <td>
        {{ toBRL(value) }}
      </td>
    </template>
    <template #isAffiliate="{ value }">
      <td>
        <div v-if="value">SIM</div>
        <div v-else>NÃO</div>
      </td>
    </template>
  </BaseTable>
</template>

<script setup lang="ts">
import BaseTable from "./BaseTable.vue";
import { defineProps, ref } from "vue";
import dayjs from "dayjs";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const { columns, rows } = defineProps(["columns", "rows"]);
const getStatus = (status) => {
  switch (status) {
    case "paid":
      return "FINALIZADO";
    default:
      return "RECUSADO";
  }
};
const isButtonDisabled = ref(false);

async function pay(id: number) {
  try {
    isButtonDisabled.value = true;
    const response = await axios.post(route("admin.saque.afiliados.aprovar"), {
      withdraw: id,
    });
    toast.success(response.data.message);
  } catch (err) {
    toast.error(err.response.data.message);
  } finally {
    isButtonDisabled.value = false;
    window.location.reload();
  }
  return "";
}

async function reject(id: number) {
  try {
    isButtonDisabled.value = true;
    const response = await axios.post(route("admin.saque.afiliados.rejeitar"), {
      withdraw: id,
    });
    toast.success(response.data.message);
  } catch (err) {
    toast.error(err.response.data.message);
    // console.log(err);
  } finally {
    isButtonDisabled.value = false;
    window.location.reload();
  }
  return "";
}

const toBRL = (value) => {
  return Number(value).toLocaleString("pt-br", {
    style: "currency",
    currency: "BRL",
  });
};
</script>
