<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { ref, defineProps, watch } from "vue";
import TextBox from "@/Components/TextBox.vue";
import dayjs from "dayjs";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import Paginator from "@/Components/Paginator.vue";
import debounce from "lodash/debounce";

const columns = [
  { label: "Email", key: "email" },
  { label: "Chave Pix", key: "pix" },
  { label: "Valor", key: "amount" },
  { label: "Data", key: "updated_at" },
  { label: "Status", key: "type" },
];

const urlParams = new URLSearchParams(window.location.search);
const initialEmail = urlParams.get("email") || "";
const searchQuery = ref(initialEmail);

watch(
  searchQuery,
  debounce((value) => {
    try {
      router.get(
        route("admin.saque"),
        { email: value },
        {
          preserveState: true,
        }
      );
    } catch (error) {
      console.error("Erro na pesquisa:", error);
    }
  }, 700)
);

const showModal = ref(false);
const getStatus = (status) => {
  switch (status) {
    case "paid":
      return "FINALIZADO";
    default:
      return "RECUSADO";
  }
};

async function approveWithdraw(withdrawId) {
  try {
    const { data } = await axios.post(route("admin.saque.aprovar"), {
      withdraw: withdrawId,
    });
    if (data.success === "error") {
      toast.error(data.message);
      return;
    }
    toast.success("Saque aprovado com sucesso!");
    window.location.reload();
  } catch (error) {
    console.log("erro interno");
  }
}

async function reject(withdrawId) {
  try {
    const { data } = await axios.post(route("admin.saque.rejeitar"), {
      withdraw: withdrawId,
    });
    if (data.success === "error") {
      toast.error(data.message);
      return;
    }
    toast.success("Saque recusado com sucesso!");
    window.location.reload();
  } catch (error) {
    // alert(error);
  }
}

const toBRL = (value) => {
  return Number(value).toLocaleString("pt-br", {
    style: "currency",
    currency: "BRL",
  });
};
const { withdraws, totalToday, totalAmount } = defineProps([
  "withdraws",
  "totalAmount",
  "totalToday",
]);
const rows = withdraws.data.map((withdraw) => {
  return {
    ...withdraw,
    email: withdraw.user?.email,
    pix: withdraw.user?.document,
  };
});
</script>

<template>
  <Head title="Admin Saques" />

  <AuthenticatedLayout>
    <div class="text-4xl text-white font-bold mb-5">Saques</div>
    <div class="flex justify-between my-4">
      <div class="">
        <div class="font-bold text-white uppercase mb-1">Pesquisar saque</div>
        <input
          type="text"
          class="admin-input"
          placeholder="Digite o email do usuário..."
          v-model="searchQuery"
        />
      </div>
      <!-- <div class="flex gap-x-5">
        <TextBox
          label="CAIXA DA CASA"
          :value="toBRL(totalAmount)"
          value-text="text-center text-green-500"
        />
        <TextBox
          label="total de saques hoje"
          :value="toBRL(totalToday)"
          value-text="text-center text-red-500"
        />
      </div> -->
    </div>
    <BaseTable
      hideActions
      class="table-xs mt-6 h-3/4"
      :columns="columns"
      :rows="rows"
    >
      <template #actions="{ value }">
        <td>
          <div class="no-wrap text-xs cursor-pointer">
            <div v-if="value.type === 'pending'" class="flex gap-x-2">
              <button
                @click="approveWithdraw(value.id)"
                class="badge w-24 font-bold rounded-sm badge-success no-wrap text-black whitespace-nowrap text-xs cursor-pointer"
              >
                PAGAR
              </button>
              <button
                @click="reject(value.id)"
                class="badge w-24 font-bold rounded-sm bg-red-600 border-0 no-wrap text-black whitespace-nowrap text-xs cursor-pointer"
              >
                RECUSAR
              </button>
            </div>
            <div
              v-else
              class="badge w-24 rounded-sm border-0 text-xs font-bold text-white"
              :class="{
                'bg-red-600': value.type === 'rejected',
                'bg-green-600': value.type === 'paid',
              }"
            >
              {{ getStatus(value.type) }}
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
    </BaseTable>
    <BaseModal v-model="showModal"> teste </BaseModal>
    <Paginator :data="withdraws" class="mt-4" />
  </AuthenticatedLayout>
</template>
