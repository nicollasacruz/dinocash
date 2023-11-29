<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { ref, computed } from "vue";
import { usePage } from '@inertiajs/vue3'

const columns = [
    { label: "Nome", key: "name" },
    { label: "Email", key: "email" },
    { label: "Saldo", key: "wallet" },
    { label: "Afiliado", key: "isAffiliate" },
];

const showModal = ref(false);

const page = usePage()

const users = computed(() => page.props.users)
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
        <BaseTable class="table-xs h-3/4" :columns="columns" :rows="users">
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
            <template #wallet="{ value }">
                <td>
                    {{
                        value.toLocaleString("pt-br", {
                            style: "currency",
                            currency: "BRL",
                        })
                    }}
                </td>
            </template>
            <template #isAffiliate="{ value }">
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
