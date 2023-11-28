<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { ref } from "vue";

const columns = [
    { label: "Nome", key: "name" },
    { label: "Email", key: "email" },
    { label: "Saldo", key: "saldo" },
    { label: "Afiliado", key: "afiliado" },
];
const rows = [
    {
        name: "John Doe",
        email: "email@teste.com",
        saldo: 100,
        afiliado: true,
    },
    {
        name: "John Doe",
        email: "email@teste.com",
        saldo: 100,
        afiliado: true,
    },{
        name: "John Doe",
        email: "email@teste.com",
        saldo: 100,
        afiliado: true,
    },{
        name: "John Doe",
        email: "email@teste.com",
        saldo: 100,
        afiliado: true,
    },{
        name: "John Doe",
        email: "email@teste.com",
        saldo: 100,
        afiliado: true,
    },
];
const showModal = ref(false);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="text-4xl text-white font-bold">Usuários</div>
        <div class="my-4">
            <div class="font-bold text-white uppercase mb-1">
                Pesquisar usuário
            </div>
            <input
                type="text"
                class="admin-input "
                placeholder="Digite o email do usuário... "
            />
        </div>
        <BaseTable class="table-xs" :columns="columns" :rows="rows">
            <template #actions="{ value }">
                <td>
                    <div
                        @click="showModal = true"
                        class="badge badge-success no-wrap text-white whitespace-nowrap text-xs cursor-pointer"
                    >
                        GERENCIAR USUÁRIO
                    </div>
                </td>
            </template>
            <template #saldo="{ value }">
                <td>
                    {{
                        value.toLocaleString("pt-br", {
                            style: "currency",
                            currency: "BRL",
                        })
                    }}
                </td>
            </template>
            <template #afiliado="{ value }">
                <td>
                    <div v-if="value">SIM</div>
                    <div v-else>NÃO</div>
                </td>
            </template>
        </BaseTable>
        <BaseModal v-model="showModal" title="Gerenciar Afiliado">
            teste
        </BaseModal>
    </AuthenticatedLayout>
</template>
