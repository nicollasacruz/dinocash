<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { ref, defineProps } from "vue";
import TextBox from "@/Components/TextBox.vue";
import dayjs from "dayjs";
const columns = [
    { label: "Nome", key: "name" },
    { label: "Email", key: "email" },
    { label: "Saldo", key: "wallet" },
    { label: "Afiliado", key: "isAffiliate" },
];
const showModal = ref(false);
const selectedTab = ref(1);
const { affiliates, affiliatesWithdraws } = defineProps([
    "affiliates",
    "affiliatesWithdraws",
]);
console.log(affiliatesWithdraws);
const toBRL = (value) => {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div role="tablist" class="tabs tabs-bordered inline">
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
        <div class="flex justify-between my-4">
            <div class="">
                <div class="font-bold text-white uppercase mb-1">
                    Pesquisar afiliado
                </div>
                <input
                    type="text"
                    class="admin-input"
                    placeholder="Digite o email do afiliado... "
                />
            </div>
            <div class="flex gap-x-5">
                <TextBox
                    label="total de saques hoje"
                    :value="toBRL(affiliatesWithdraws)"
                    value-text="text-center text-red-500"
                />
            </div>
        </div>
        <BaseTable class="mt-7 table-xs" :columns="columns" :rows="affiliates">
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
<style>
.tab-active {
    border-bottom: 2px solid #fff !important;
}
</style>
