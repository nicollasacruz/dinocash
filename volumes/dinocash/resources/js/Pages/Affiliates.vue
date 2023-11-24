<script setup lang="ts">
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
const selectedTab = ref(1);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div role="tablist" class="tabs tabs-bordered  inline">
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

        <BaseTable class="mt-5" :columns="columns" :rows="rows">
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
<style>
.tab-active {
    border-bottom: 2px solid #fff !important;
}
</style>