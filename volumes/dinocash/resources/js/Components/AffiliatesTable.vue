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
    <BaseModal class="z-10" v-if="showModal" v-model="showModal">
        <UserForm typeForm="affiliate"
            :user="selectedUser"          
            @submit="submit"
        />
    </BaseModal>
    
</template>
<script setup lang="ts">
import BaseTable from "./BaseTable.vue";
import BaseModal from "./BaseModal.vue";
import { ref, defineProps } from "vue";
import UserForm from "./UserForm.vue";
import axios from "axios";

const { columns, rows } = defineProps(["columns", "rows"]);
const showModal = ref(false);
const showData = ref(false);
const selectedUser = ref(null);
const type = ref(null);
import dayjs from "dayjs";
function selectUser(user) {
    showModal.value = true;
    selectedUser.value = user;
}
const data = ref(null);

function toBRL(value) {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
}

function submit(values) {
    console.log(values);
    const payload = {

    }
    axios
        .patch(route('admin.usuarios.update'), values)
        .then((response) => {
            console.log(response.data);
            showModal.value = false;
        })
        .catch((error) => {
            console.log(error);
        });
}
</script>
