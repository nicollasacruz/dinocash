<script setup lang="ts">
import AffiliateLayout from "@/Layouts/AffiliateLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { ref, defineProps } from "vue";
import TextBox from "@/Components/TextBox.vue";
import dayjs from "dayjs";

const columns = [
    { label: "Nº Fatura", key: "id" },
    { label: "Valor", key: "amount" },
    { label: "Status", key: "status" },
    { label: "Data", key: "updated_at" },
    { label: "Faturado Em", key: "invoicedAt" },
];
const { affiliatesInvoices } = defineProps(["affiliatesInvoices"]);
console.log(affiliatesInvoices, 'affiliatesInvoices');

const showModal = ref(false);
const toBRL = (value) => {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
};
const getStatus = (status) => {
    console.log(status);
    switch (status) {
        case "closed":
            return "FECHADA";
        default:
            return "ABERTA";
    }
};
const rows = [];
</script>

<template>
    <Head title="Afiliado Faturas" />

    <AffiliateLayout>
        <div class="text-4xl text-white font-bold mb-3">Faturas</div>
        <div class="my-3 flex justify-between">
            <div>
                <div class="font-bold text-white uppercase mb-1">
                    Pesquisar faturas
                </div>
                <input
                    type="text"
                    class="admin-input"
                    placeholder="Digite o email do usuário... "
                />
            </div>
            <div class="flex gap-x-5">
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
            </div>
        </div>
        <BaseTable
            hide-actions
            :columns="columns"
            :rows="affiliatesInvoices"
            class="table-xs mt-6 h-3/4"
        >
            <template #updated_at="{ value }">
                <td>
                    {{ dayjs(value).format("DD/MM/YYYY HH:mm:ss") }}
                </td>
            </template>
                        <template #invoicedAt="{ value }">
                <td>
                    {{ value ? dayjs(value).format("DD/MM/YYYY HH:mm:ss") : null}}
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
            <template #status="{ value }">
                <td>
                    <div class="no-wrap text-xs cursor-pointer">
                        <div
                            class="badge w-24 rounded-sm border-0 text-xs font-bold text-white"
                            :class="{
                                'bg-gray-600': value === 'open',
                                'bg-green-600': value === 'closed',
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
