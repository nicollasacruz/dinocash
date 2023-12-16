<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import { ref, defineProps } from "vue";
import TextBox from "@/Components/TextBox.vue";
import dayjs from "dayjs";

const columns = [
    { label: "Email", key: "email" },
    { label: "Chave Pix", key: "document" },
    { label: "Valor", key: "amount" },
    { label: "Data", key: "updated_at" },
    { label: "Status", key: "type" },
];
const { deposits, totalToday, totalAmount } = defineProps([
    "deposits",
    "totalToday",
    "totalAmount",
]);
const depositsRow = deposits.map((deposit) => {
    return {
        ...deposit,
        document: deposit.user.document,
        email: deposit.user.email,
    };
});
const toBRL = (value) => {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
};
const getStatus = (status) => {
  switch (status) {
    case "paid":
      return "FINALIZADO";
    default:
      return "PENDENTE";
  }
};
</script>

<template>
    <Head title="Admin Depósitos" />

    <AuthenticatedLayout>
        <div class="text-4xl text-white font-bold mb-3">Depósitos</div>
        <div class="my-3 flex flex-col lg:flex-row justify-between">
            <div>
                <div class="font-bold text-white uppercase mb-1">
                    Pesquisar depósitos
                </div>
                <input
                    type="text"
                    class="admin-input"
                    placeholder="Digite o email do usuário... "
                />
            </div>
            <div class="flex flex-col md:flex-row gap-x-5 mt-2 lg:mt-0">
                <TextBox
                    label="CAIXA DA CASA"
                    :value="toBRL(totalAmount)"
                    value-text="text-center text-green-500"
                    label-text="text-xs text-white font-bold"
                />
                <TextBox
                    label="total de depósitos hoje"
                    :value="toBRL(totalToday)"
                    value-text="text-center text-green-500"
                    label-text="text-xs text-white font-bold"
                />
            </div>
        </div>
        <BaseTable
            hide-actions
            :columns="columns"
            :rows="depositsRow"
            class="table-xs mt-6 h-3/4"
        >
            <template #updated_at="{ value }">
                <td class="py-0">
                    {{ dayjs(value).format("DD/MM/YYYY HH:mm:ss") }}
                </td>
            </template>
            <template #amount="{ value }">
                <td class="py-0">
                    {{
                        value.toLocaleString("pt-br", {
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
                                'bg-red-600': value === 'pending',
                                'bg-green-600': value === 'paid',
                            }"
                        >
                            {{ getStatus(value) }}
                        </div>
                    </div>
                </td>
            </template>
        </BaseTable>
    </AuthenticatedLayout>
</template>
