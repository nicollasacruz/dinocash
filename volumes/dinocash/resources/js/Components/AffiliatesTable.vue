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
        <UserForm
            :user="selectedUser"
            @get-histories="getHistories"
            @get-commissions="getCommissions"
            @get-movements="getMovements"
            @delete-user="deleteUser"
            @submit="submit"
        />
    </BaseModal>
    <BaseModal v-if="showData" v-model="showData" class="z-30">
        <div class="text-white text-xl font-bold mb-3 text-center">
            <span v-if="type === 'history'"> Histórico de Jogos </span>
            <span v-else-if="type === 'commissions'">
                Histórico de Comissões
            </span>
            <span v-else> Histórico de Movimentações </span>
        </div>
        <table v-if="type === 'history'" class="table table-xs">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Distancia</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in data" :key="item.id">
                    <td>{{ item.type }}</td>
                    <td>{{ item.amount }}</td>
                    <td>{{ item.distance }}</td>
                </tr>
            </tbody>
        </table>
        <table v-else-if="type === 'commissions'" class="table table-xs">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Distancia</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in data" :key="item.id">
                    <td>{{ item.type }}</td>
                    <td>{{ item.amount }}</td>
                    <td>{{ item.distance }}</td>
                </tr>
            </tbody>
        </table>
        <table v-else class="table table-xs">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Valor</th>
                    <th>Data Aprovação</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in data" :key="item.id">
                    <td>{{ item.type }}</td>
                    <td>{{ toBRL(item.amount) }}</td>
                    <td>{{ dayjs(item.updated_at).format("DD/MM/YYYY") }}</td>
                </tr>
            </tbody>
        </table>
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
function getHistories(user) {
    axios
        .get("/admin/afiliados/listGameHistory", {
            params: {
                user,
            },
        })
        .then((response) => {
            data.value = response.data.transactions;
            showData.value = true;
            type.value = "history";
            console.log(response.data);
        })
        .catch((error) => {
            console.log(error);
        });
}
function getCommissions(user) {
    axios
        .get("/admin/afiliados/listAffiliateHistory", {
            params: {
                user,
            },
        })
        .then((response) => {
            data.value = response.data.transactions;
            showData.value = true;
            type.value = "commissions";
            console.log(response.data);
        })
        .catch((error) => {
            console.log(error);
        });
}
function getMovements(user) {
    axios
        .get("/admin/afiliados/listTransactions", {
            params: {
                user,
            },
        })
        .then((response) => {
            data.value = response.data.transactions;
            showData.value = true;
            type.value = "movements";
            console.log(response.data);
        })
        .catch((error) => {
            console.log(error);
        });
}
function deleteUser(user) {
    console.log(user);
    axios
        .delete("/admin/afiliados/" + user)
        .then((response) => {
            console.log(response.data);
            showModal.value = false;
        })
        .catch((error) => {
            console.log(error);
        });
}
function toBRL(value) {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
}

function submit(values) {
    console.log(values);
    axios
        .patch("/admin/afiliados", values)
        .then((response) => {
            console.log(response.data);
            showModal.value = false;
        })
        .catch((error) => {
            console.log(error);
        });
}
</script>
