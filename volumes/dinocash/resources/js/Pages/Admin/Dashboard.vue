<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { ref, defineProps } from "vue";
import CurrencyBox from "@/Components/CurrencyBox.vue";
import TextBox from "@/Components/TextBox.vue";
import { UserIcon } from "@heroicons/vue/24/solid";
import UserForm from "@/Components/UserForm.vue";
const {
    activeSessions,
    totalUsers,
    lastUsers,
    lossLast30,
    lossToday,
    lossTotal,
    payoutLast30,
    payoutToday,
    payoutTotal,
} = defineProps([
    "activeSessions",
    "totalUsers",
    "lastUsers",
    "lossLast30",
    "lossToday",
    "lossTotal",
    "payoutLast30",
    "payoutToday",
    "payoutTotal",
]);
const showModal = ref(false);
const columns = [
    { label: "Nome", key: "name" },
    { label: "Email", key: "email" },
    { label: "Saldo", key: "wallet" },
    { label: "Afiliado", key: "isAffiliate" },
];
const last5Users = lastUsers.slice(0, 5);
const selectedUser = ref(null);
console.log(last5Users);
function selectUser(user) {
    console.log(user);
    showModal.value = true;
    selectedUser.value = user;
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="flex justify-between">
            <div class="text-4xl text-white font-bold mb-5">Dashboard</div>
            <div class="flex gap-x-5 -mt-4">
                <TextBox
                    label="Online"
                    :value="activeSessions"
                    label-text="text-green-500"
                >
                    <template #icon>
                        <UserIcon class="w-5 fill-green-500" />
                    </template>
                </TextBox>
                <TextBox label="Cadastros" :value="totalUsers">
                    <template #icon>
                        <UserIcon class="w-5" />
                    </template>
                </TextBox>
            </div>
        </div>
        <div class="h-52 bg-black text-white text-center">Gráfico</div>

        <div class="grid grid-cols-5 gap-x-2 mt-4">
            <CurrencyBox label="Lucro em 30 dias" :value="payoutLast30" />
            <CurrencyBox
                label="Prejuizo em 30 dias"
                :value="lossLast30"
                negative
            />
            <CurrencyBox label="Lucro Total" :value="payoutTotal" />
            <CurrencyBox label="Prejuizo Total" :value="lossTotal" negative />
            <CurrencyBox label="Lucro do dia" :value="payoutToday" />
        </div>
        <div class="text-2xl font-bold text-white mt-6 mb-2">
            Últimos Cadastros
        </div>
        <BaseTable class="table-xs" :columns="columns" :rows="last5Users">
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
            <UserForm :user="selectedUser" />
        </BaseModal>
    </AuthenticatedLayout>
</template>
