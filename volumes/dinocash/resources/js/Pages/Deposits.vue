<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { ref } from "vue";
import TextBox from "@/Components/TextBox.vue";

const columns = [
    { label: "Email", key: "email" },
    { label: "Chave Pix", key: "pix" },
    { label: "Valor", key: "valor" },
    { label: "Data", key: "data" },
    { label: "Status", key: "status" },
];
const rows = [
    {
        email: "email@teste.com",
        pix: "teste",
        valor: 'R$ 100,00',
        data: "10/10/2021",
        status: 1,
    },
    {
        email: "email@teste.com",
        pix: "teste",
        valor: 'R$ 100,00',
        data: "10/10/2021",
        status: 1,
    },
    {
        email: "email@teste.com",
        pix: "teste",
        valor: 'R$ 100,00',
        data: "10/10/2021",
        status: 1,
    },
    {
        email: "email@teste.com",
        pix: "teste",
        valor: 'R$ 100,00',
        data: "10/10/2021",
        status: 1,
    },
    {
        email: "email@teste.com",
        pix: "teste",
        valor: 'R$ 100,00',
        data: "10/10/2021",
        status: 1,
    },
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
                        value="R$ 10.000"
                        value-text="text-center text-green-500"
                    />
                    <TextBox
                        label="total de depósitos hoje"
                        value="R$ 10.000"
                        value-text="text-center text-green-500"
                    />
                </div>
            </div>
        </div>
        <BaseTable hide-actions :columns="columns" :rows="rows" class="table-xs mt-6">
            <template #status="{ value }">
                <td>
                    <div
                        class="badge badge-success no-wrap text-white whitespace-nowrap text-xs cursor-pointer"
                    >
                        {{ getStatus(value) }}
                    </div>
                </td>
            </template>
        </BaseTable>
        <BaseModal v-model="showModal" title="Gerenciar Afiliado">
            teste
        </BaseModal>
    </AuthenticatedLayout>
</template>
