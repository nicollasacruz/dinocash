<template>
    <Head title="Admin Usuarios" />

    <AuthenticatedLayout>
        <div class="text-4xl text-white font-bold">Usuários</div>
        <div class="my-4 flex gap-x-4">
            <div>
                <div class="font-bold text-white uppercase mb-1">
                    Pesquisar usuário
                </div>
                <input
                    type="text"
                    class="admin-input"
                    placeholder="Digite o email do usuário... "
                    v-model="searchQuery"
                />
            </div>
            <div>
                <div class="font-bold text-white uppercase mb-1">
                    Usuário banido
                </div>
                <select
                    type="text"
                    class="admin-input w-full"
                    placeholder="Digite o email do usuário... "
                    v-model="isBanned"
                >
                    <option value="all">Todos</option>
                    <option value="banned">Banidos</option>
                    <option value="unbanned">Não Banidos</option>
                </select>
            </div>
        </div>
        <BaseTable
            class="table-xs h-3/4"
            :columns="columns"
            :rows="props.users.data"
        >
            <template #created_at="{ value }">
                <td class="py-0">
                    {{ dayjs(value).format("DD/MM/YYYY HH:mm:ss") }}
                </td>
            </template>
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

        <Paginator :data="props.users" class="mt-4" />

        <BaseModal v-if="showModal" v-model="showModal">
            <UserForm @submit="submit" :user="selectedUser" typeForm="user" />
        </BaseModal>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import Paginator from "@/Components/Paginator.vue";
import { ref, computed, watch } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
import UserForm from "@/Components/UserForm.vue";
import axios from "axios";
import dayjs from "dayjs";
import debounce from "lodash/debounce";

const columns = [
    { label: "Data", key: "created_at" },
    // { label: "Nome", key: "name" },
    { label: "Email", key: "email" },
    { label: "Saldo", key: "wallet" },
    // { label: "Afiliado", key: "isAffiliate" },
];

const showModal = ref(false);
const selectedUser = ref(null);
const page = usePage();

const props = defineProps({
    users: Object,
});

function selectUser(user) {
    showModal.value = true;
    selectedUser.value = user;
}

function submit(values) {
    axios
        .patch(route("admin.usuarios.update"), values)
        .then((response) => {
            showModal.value = false;
        })
        .catch((error) => {
            console.log(error);
        });
}

const urlParams = new URLSearchParams(window.location.search);
const initialEmail = urlParams.get("email") || "";
const searchQuery = ref(initialEmail);
const isBanned = ref("all");
watch(
    [searchQuery, isBanned],
    debounce(([email, banned]) => {
        try {
            router.get(
                route("admin.usuarios"),
                { email: email, status: banned },
                {
                    preserveState: true,
                }
            );
        } catch (error) {
            console.error("Erro na pesquisa:", error);
        }
    }, 700)
);

console.log(props.users, "users");
</script>
