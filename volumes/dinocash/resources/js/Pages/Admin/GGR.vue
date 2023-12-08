<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import "flatpickr/dist/flatpickr.min.css";
import { ref, defineProps, onMounted } from "vue";
import TextBox from "@/Components/TextBox.vue";
import CurrencyBox from "@/Components/CurrencyBox.vue";
import { format } from "date-fns";
import BaseInput from "@/Components/BaseInput.vue";
import BaseTable from "@/Components/BaseTable.vue";

const columns = [
    { label: "Data e hora", key: "date" },
    { label: "Fatura", key: "fat" },
    { label: "Valor", key: "value" },
];
const rows = [
    { date: "01/01/2021", fat: "123456", value: "R$ 100,00" },
    { date: "01/01/2021", fat: "123456", value: "R$ 100,00" },
    { date: "01/01/2021", fat: "123456", value: "R$ 100,00" },
    { date: "01/01/2021", fat: "123456", value: "R$ 100,00" },
    { date: "01/01/2021", fat: "123456", value: "R$ 100,00" },
    { date: "01/01/2021", fat: "123456", value: "R$ 100,00" },
    { date: "01/01/2021", fat: "123456", value: "R$ 100,00" },
    { date: "01/01/2021", fat: "123456", value: "R$ 100,00" },
];
const selectedTab = ref(1);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="text-4xl text-white font-bold">GGR</div>
        <div class="flex">
            <div>
                <base-input
                    label-style="font-bold text-xs text-white uppercase"
                    label="Pagamento GGR"
                    class="flex-0"
                />
            </div>
            <div class="flex flex-col justify-end">
                <button
                    class="bg-green-500 text-black uppercase font-bold py-1 px-3 rounded-md ml-2"
                >
                    Pagar Agora
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-2">
            <CurrencyBox label="Saldo Atual GGR" :value="'200'" />
            <TextBox label="Porcentagem GGR" value="12%" />
            <CurrencyBox label="Total GGR Pago" :value="'200'" />
            <CurrencyBox label="Total GGR" :value="'200'" />
        </div>
        <BaseTable class="mt-6" hide-actions :columns="columns" :rows="rows" v-if="selectedTab === 1"> </BaseTable>
        <BaseTable class="mt-6" hide-actions :columns="columns" :rows="rows" v-if="selectedTab === 2"> </BaseTable>
        <BaseTable class="mt-6" hide-actions :columns="columns" :rows="rows" v-if="selectedTab === 3"> </BaseTable>
        <div role="tablist" class="tabs tabs-bordered inline">
            <a
                role="tab"
                :class="selectedTab === 1 ? 'tab-active' : ''"
                class="text-4xl text-white font-bold tab h-12"
                @click="selectedTab = 1"
            >
                Movimentação Geral
            </a>
            <a
                role="tab"
                :class="selectedTab === 2 ? 'tab-active' : ''"
                class="text-4xl text-white font-bold tab h-12"
                @click="selectedTab = 2"
            >
                Faturas
            </a>
            <a
                role="tab"
                :class="selectedTab === 3 ? 'tab-active' : ''"
                class="text-4xl text-white font-bold tab h-12"
                @click="selectedTab = 3"
            >
                Pagamentos Realizados
            </a>
        </div>
    </AuthenticatedLayout>
</template>
