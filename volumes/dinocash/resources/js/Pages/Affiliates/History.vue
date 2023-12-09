<script setup lang="ts">
import AffiliateLayout from "@/Layouts/AffiliateLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import { defineProps } from "vue";
import TextBox from "@/Components/TextBox.vue";
import dayjs from "dayjs";

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
console.log(affiliateHistory);
const getStatus = (status) => {
    console.log(status);
    switch (status) {
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
        <div class="text-4xl text-white font-bold mb-3">Histórico de pagamentos</div>
        <div class="my-3 flex justify-between">
            <div>
                <div class="font-bold text-white uppercase mb-1">
                    Pesquisar
                </div>
                <input
                    type="text"
                    class="admin-input"
                    placeholder="Digite o email do usuário... "
                />
            </div>
        </div>
        <BaseTable
            hide-actions
            :columns="columns"
            :rows="affiliateHistory"
            class="table-xs mt-6 h-3/4"
        >
            <template #updated_at="{ value }">
                <td>
                    {{ dayjs(value).format("DD/MM/YYYY hh:mm:ss") }}
                </td>
            </template>
            <template #invoicedAt="{ value }">
                <td>
                    {{ value ? dayjs(value).format("DD/MM/YYYY hh:mm:ss") : null}}
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
                                'bg-yellow-600': value === 'WITHDRAW',
                            }"
                        >
                            {{ getStatus(value) }}
                        </div>
                    </div>
                </td>
            </template>
        </BaseTable>
    </AffiliateLayout>
</template>
