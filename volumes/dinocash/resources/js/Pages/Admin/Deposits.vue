<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
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
const { deposits, totalToday, totalAmount } = defineProps(["deposits", "totalToday", "totalAmount"]);
const depositsRow = deposits.map((deposit) => {
    return {
        ...deposit,
        document: deposit.user.document,
        email: deposit.user.email,
    };
});
const showModal = ref(false);
const toBRL = (value) => {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
};
const getStatus = (status) => {
    switch (status) {
        case 'paid':
            return "FINALIZADO";
        default:
            return "RECUSADO";
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="text-4xl text-white font-bold mb-3">Depósitos</div>
        <div class="my-3 flex justify-between">
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
            <div class="flex gap-x-5">
                <div class="flex gap-x-5">
                    <TextBox
                        label="CAIXA DA CASA"
                        :value="toBRL(totalAmount)"
                        value-text="text-center text-green-500"
                    />
                    <TextBox
                        label="total de depósitos hoje"
                        :value="
                            toBRL(totalToday)
                        "
                        value-text="text-center text-green-500"
                    />
                </div>
            </div>
        </div>
        <BaseTable
            hide-actions
            :columns="columns"
            :rows="depositsRow"
            class="table-xs mt-6 h-3/4"
        >
            <template #updated_at="{ value }">
                <td>
                    {{ dayjs(value).format("DD/MM/YYYY") }}
                </td>
            </template>
            <template #amount="{ value }">
                <td>
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
                    <div v-if="value !== 'paid'" class="flex gap-x-2">
                 
                        <div
                            class="badge w-24 font-bold rounded-sm badge-success no-wrap text-black whitespace-nowrap text-xs cursor-pointer"
                        >
                            PAGAR
                        </div>
                        <div
                            class="badge w-24 font-bold rounded-sm bg-red-600 border-0 no-wrap text-white whitespace-nowrap text-xs cursor-pointer"
                        >
                            RECUSAR
                        </div>
                    </div>
                    <div
                        v-else
                        class="badge w-24 rounded-sm bg-green-600 border-0 text-xs font-bold text-white"
                    >
                        {{ getStatus(value) }}
                    </div>
                </div>
            </td>
            </template>
        </BaseTable>
        <BaseModal v-model="showModal">
            teste
        </BaseModal>
    </AuthenticatedLayout>
</template>
