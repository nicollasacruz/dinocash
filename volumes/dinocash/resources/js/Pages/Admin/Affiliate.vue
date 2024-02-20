<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, defineProps, computed, watch } from "vue";
import TextBox from "@/Components/TextBox.vue";
import AffiliatesTable from "@/Components/AffiliatesTable.vue";
import PaymentsTable from "@/Components/PaymentsTable.vue";
import Paginator from "@/Components/Paginator.vue";
import axios from "axios";
import debounce from "lodash/debounce";
import Checkbox from "@/Components/Checkbox.vue";
import { watchEffect } from "vue";


const columns = computed(() =>
  selectedTab.value === 1
    ? [
      { label: "Nome", key: "name" },
      { label: "Email", key: "email" },
      { label: "Saldo", key: "walletAffiliate" },
      // { label: "Saldo Pendente", key: "paymentPending" },
    ]
    : [
      { label: "Email", key: "email" },
      { label: "Valor", key: "amount" },
      { label: "Data", key: "updated_at" },
      { label: "Tipo de Chave", key: "pixKey" },
      { label: "Chave", key: "pixValue" },
      { label: "Status", key: "type" },
    ]
);
const { affiliates, affiliatesWithdrawsToday, affiliatesWithdrawsList } =
  defineProps(["affiliates", "affiliatesWithdrawsToday", "affiliatesWithdrawsList"]);
const paymentsRow = affiliatesWithdrawsList;

const toBRL = (value) => {
  return Number(value).toLocaleString("pt-br", {
    style: "currency",
    currency: "BRL",
  });
};
const urlParams = new URLSearchParams(window.location.search);
const initialEmail = urlParams.get("email") || "";
const initialStatus = urlParams.get("status") || "all";
const searchQuery = ref(initialEmail);
const statusQuery = ref(initialStatus);
const selectedTab = ref(loadSelectedTab());

watch([searchQuery, statusQuery], ([newSearchQuery, newStatusQuery], [oldSearchQuery, oldStatusQuery]) => {
  debounce(() => {
    try {
      router.get(
        route("admin.afiliados"),
        { email: newSearchQuery, status: newStatusQuery },
        {
          preserveState: true,
        }
      );
    } catch (error) {
      console.error("Erro na pesquisa:", error);
    }
  }, 700)();
});

function saveSelectedTab(selectedTab) {
  localStorage.setItem('selectedTab', selectedTab);
}

function loadSelectedTab() {
  const savedTab = localStorage.getItem('selectedTab');
  return savedTab ? parseInt(savedTab) : 1;
}

watch(selectedTab, (newTab) => {
  saveSelectedTab(newTab);
});

</script>

<template>
  <Head title="Admin Afiliados" />

  <AuthenticatedLayout>
    <div role="tablist" class="tabs tabs-bordered inline">
      <a role="tab" :class="selectedTab === 1 ? 'tab-active' : ''" class="text-4xl text-white font-bold tab h-12"
        @click="selectedTab = 1; saveSelectedTab(1)">
        Afiliados
      </a>
      <a role="tab" :class="selectedTab === 2 ? 'tab-active' : ''" class="text-4xl text-white font-bold tab h-12"
        @click="selectedTab = 2; saveSelectedTab(2)">
        Pagamentos
      </a>

    </div>
    <div class="flex justify-between my-4">
      <div class="flex flex-row">
        <div>
          <div class=" font-bold text-white uppercase mb-1">
            Pesquisar afiliado
          </div>
          <input type="text" class="admin-input" placeholder="Digite o email do afiliado..." v-model="searchQuery" />
        </div>
        <div>
          <div v-if="selectedTab === 2" class="font-bold text-white uppercase mb-1">
            Filtrar statuus
          </div>
          <select v-if="selectedTab === 2" v-model="statusQuery" class="admin-input">
            <option value="all">Todos</option>
            <option value="paid">Pago</option>
            <option value="pending">Pendente</option>
            <option value="rejected">Recusado</option>
          </select>
        </div>
      </div>
      <div class="flex gap-x-5">
        <TextBox label="total de saques hoje" :value="toBRL(affiliatesWithdrawsToday)"
          value-text="text-center text-red-500" />
      </div>
    </div>
    <affiliates-table v-if="selectedTab === 1" :columns="columns" :rows="affiliates.data" />
    <payments-table v-else :columns="columns" :rows="affiliatesWithdrawsList.data" />
    <Paginator v-if="selectedTab === 1" :data="affiliates" class="mt-4" />
    <Paginator v-else :data="affiliatesWithdrawsList" class="mt-4" />
  </AuthenticatedLayout>
</template>

<style>
.tab-active {
  border-bottom: 2px solid #fff !important;
}
</style>
