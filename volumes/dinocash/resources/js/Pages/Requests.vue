<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { ref, defineProps } from "vue";
import TextBox from "@/Components/TextBox.vue";
import dayjs from "dayjs";
const columns = [
    { label: "Email", key: "email" },
    { label: "Chave Pix", key: "pix" },
    { label: "Valor", key: "amount" },
    { label: "Data", key: "updated_at" },
    { label: "Status", key: "status" },
];
const showModal = ref(false);
const getStatus = (status) => {
    switch (status) {
        case 1:
            return "FINALIZADO";
        case 2:
            return "Reprovado";
        case 3:
            return "Pendente";
        default:
            return "Pendente";
    }
};
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
console.log(withdraws);
const rows = withdraws.map((withdraw) => {
    return {
        ...withdraw,
        email: withdraw.user.email,
        pix: withdraw.user.document,
    };
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="text-4xl text-white font-bold mb-5">Saques</div>
        <div class="flex justify-between my-4">
            <div class="">
                <div class="font-bold text-white uppercase mb-1">
                    Pesquisar saque
                </div>
                <input
                    type="text"
                    class="admin-input"
                    placeholder="Digite o email do usuário... "
                />
            </div>
            <div class="flex gap-x-5">
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
            </div>
        </div>
        <BaseTable
            hideActions
            class="table-xs mt-6"
            :columns="columns"
            :rows="rows"
        >
            <template #status="{ value }">
                <td>
                    <div
                        class="badge badge-success no-wrap text-white whitespace-nowrap text-xs cursor-pointer"
                    >
                        {{ getStatus(value) }}
                    </div>
                </td>
            </template>
            <template #updated_at="{ value }">
                <td>
                    {{ dayjs(value).format("DD/MM/YYYY") }}
                </td>
            </template>
            <template #amount="{ value }">
                <td>
                    {{ toBRL(value) }}
                </td>
            </template>
        </BaseTable>
        <BaseModal v-model="showModal" title="Gerenciar Afiliado">
            teste
        </BaseModal>
    </AuthenticatedLayout>
</template>
