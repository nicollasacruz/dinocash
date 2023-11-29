<template>
    <BaseTable class="mt-7 table-xs h-3/4" :columns="columns" :rows="rows">
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
        
    </BaseModal>
</template>
<script setup lang="ts">
import BaseTable from "./BaseTable.vue";
import BaseModal from "./BaseModal.vue";
import { ref, defineProps } from "vue";
import dayjs from "dayjs";
const { columns, rows } = defineProps(["columns", "rows"]);
const showModal = ref(false);
</script>
