<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, defineProps, computed } from "vue";
import TextBox from "@/Components/TextBox.vue";
import AffiliatesTable from "@/Components/AffiliatesTable.vue";
import PaymentsTable from "@/Components/PaymentsTable.vue";

const selectedTab = ref(1);
const columns = computed(() =>
    selectedTab.value === 1
        ? [
              { label: "Nome", key: "name" },
              { label: "Email", key: "email" },
              { label: "Saldo", key: "wallet" },
              { label: "Afiliado", key: "isAffiliate" },
          ]
        : [
              { label: "Nome", key: "name" },
              { label: "Email", key: "email" },
              { label: "Valor", key: "amount" },
              { label: "Data", key: "updated_at" },
              { label: "Status", key: "type" },
          ]
);
const { affiliates, affiliatesWithdraws, affiliatesWithdrawsList } =
    defineProps([
        "affiliates",
        "affiliatesWithdraws",
        "affiliatesWithdrawsList",
    ]);
const paymentsRow = affiliatesWithdrawsList.map((payment) => {
    return {
        ...payment,
        name: payment.user.name,
        email: payment.user.email,
    };
});
console.log('af',affiliates);
const toBRL = (value) => {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div role="tablist" class="tabs tabs-bordered inline">
            <a
                role="tab"
                :class="selectedTab === 1 ? 'tab-active' : ''"
                class="text-4xl text-white font-bold tab h-12"
                @click="selectedTab = 1"
            >
                Afiliados
            </a>
            <a
                role="tab"
                :class="selectedTab === 2 ? 'tab-active' : ''"
                class="text-4xl text-white font-bold tab h-12"
                @click="selectedTab = 2"
            >
                Pagamentos
            </a>
        </div>
        <div class="flex justify-between my-4">
            <div class="">
                <div class="font-bold text-white uppercase mb-1">
                    Pesquisar afiliado
                </div>
                <input
                    type="text"
                    class="admin-input"
                    placeholder="Digite o email do afiliado... "
                />
            </div>
            <div class="flex gap-x-5">
                <TextBox
                    label="total de saques hoje"
                    :value="toBRL(affiliatesWithdraws)"
                    value-text="text-center text-red-500"
                />
            </div>
        </div>
        <affiliates-table
            v-if="selectedTab === 1"
            :columns="columns"
            :rows="affiliates"
        />
        <payments-table
            v-else
            :columns="columns"
            :rows="paymentsRow"
        />
    </AuthenticatedLayout>
</template>
<style>
.tab-active {
    border-bottom: 2px solid #fff !important;
}
</style>
