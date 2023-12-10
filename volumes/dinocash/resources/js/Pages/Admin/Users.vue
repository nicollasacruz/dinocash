<template>
    <Head title="Admin Usuarios" />

    <AuthenticatedLayout>
        <div class="text-4xl text-white font-bold">Usuários</div>
        <div class="my-4">
            <div class="font-bold text-white uppercase mb-1">
                Pesquisar usuário
            </div>
            <input
                type="text"
                class="admin-input"
                placeholder="Digite o email do usuário... "
            />
        </div>
        <BaseTable class="table-xs h-3/4" :columns="columns" :rows="users">
            <template #actions="{ value }">
                <td>
                    <div
                        @click="selectUser(value)"
                        class="badge badge-success no-wrap text-white whitespace-nowrap text-xs cursor-pointer"
                    >
                        GERENCIAR
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
        <BaseModal v-if="showModal" v-model="showModal">
            <UserForm @submit="submit" :user="selectedUser" typeForm="user" />
        </BaseModal>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import UserForm from "@/Components/UserForm.vue";
import axios from "axios";
const columns = [
    { label: "Nome", key: "name" },
    { label: "Email", key: "email" },
    { label: "Saldo", key: "wallet" },
    { label: "Afiliado", key: "isAffiliate" },
];

const showModal = ref(false);
const selectedUser = ref(null);
const page = usePage();

const users = computed(() => page.props.users) as any;

function selectUser(user) {
    console.log(user);
    showModal.value = true;
    selectedUser.value = user;
}

function submit(values) {
    console.log(values);
    axios
        .patch(route('user.update'), values)
        .then((response) => {
            console.log(response.data);
            showModal.value = false;
        })
        .catch((error) => {
            console.log(error);
        });
}
</script>
