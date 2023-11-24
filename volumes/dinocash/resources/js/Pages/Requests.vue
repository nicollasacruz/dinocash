<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { ref } from "vue";

const columns = [
    { label: "Nome", key: "name" },
    { label: "Email", key: "email" },
];
const rows = [
    {
        name: "John Doe",
        email: "email@teste.com",
    },
    {
        name: "John Doe",
        email: "email@teste.com",
    },
    {
        name: "John Doe",
        email: "email@teste.com",
    },
    {
        name: "John Doe",
        email: "email@teste.com",
    },
    {
        name: "John Doe",
        email: "email@teste.com",
    },
    {
        name: "John Doe",
        email: "email@teste.com",
    },
];
const showModal = ref(false);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="text-4xl text-white font-bold mb-5">Saques</div>

        <BaseTable :columns="columns" :rows="rows">
            <template #actions="{ value }">
                <td>
                    <div
                        @click="showModal = true"
                        class="badge badge-success no-wrap text-white whitespace-nowrap text-xs cursor-pointer"
                    >
                        GERENCIAR AFILIADO
                    </div>
                </td>
            </template>
        </BaseTable>
        <BaseModal v-model="showModal" title="Gerenciar Afiliado">
            teste
        </BaseModal>
    </AuthenticatedLayout>
</template>
