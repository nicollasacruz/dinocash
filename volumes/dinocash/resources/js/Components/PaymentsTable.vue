<template>
    <BaseTable
        hideActions
        class="mt-7 table-xs h-3/4"
        :columns="columns"
        :rows="rows"
    >
        <template #type="{ value }">
            <td>
                <div class="no-wrap text-xs cursor-pointer">
                    <div v-if="value !== 'paid'" class="flex gap-x-2">
                        <div class="text-white">
                            {{ value }}
                        </div>
                        <div
                            class="badge w-24 font-bold rounded-sm badge-success no-wrap text-black whitespace-nowrap text-xs cursor-pointer"
                        >
                            PAGAR
                        </div>
                        <div
                            class="badge w-24 font-bold rounded-sm bg-red-600 border-0 no-wrap text-white whitespace-nowrap text-xs cursor-pointer"
                        >
                            RECUSAR
                        </div>
                    </div>
                    <div
                        v-else
                        class="badge w-24 rounded-sm bg-green-600 border-0 text-xs font-bold text-white"
                    >
                        {{ getStatus(value) }}
                    </div>
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
</template>
<script setup lang="ts">
import BaseTable from "./BaseTable.vue";
import { defineProps } from "vue";
const { columns, rows } = defineProps(["columns", "rows"]);
const getStatus = (status) => {
    switch (status) {
        case 'paid':
            return "FINALIZADO";
        default:
            return "RECUSADO";
    }
};
</script>
