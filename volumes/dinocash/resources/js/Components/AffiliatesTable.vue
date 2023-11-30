<template>
    <BaseTable class="mt-7 table-xs h-3/4" :columns="columns" :rows="rows">
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
</template>
<script setup lang="ts">
import BaseTable from "./BaseTable.vue";
import BaseModal from "./BaseModal.vue";
import { ref, defineProps } from "vue";
import UserForm from "./UserForm.vue";
const { columns, rows } = defineProps(["columns", "rows"]);
const showModal = ref(false);
const selectedUser = ref(null);
function selectUser(user) {
    showModal.value = true;
    selectedUser.value = user;
}
</script>
