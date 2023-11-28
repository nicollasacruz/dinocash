<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import BaseTable from "@/Components/BaseTable.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { ref } from "vue";
import CurrencyBox from "@/Components/CurrencyBox.vue";
import TextBox from "@/Components/TextBox.vue";
import { UserIcon } from "@heroicons/vue/24/solid";

const showModal = ref(false);
const columns = [
    { label: "Nome", key: "name" },
    { label: "Email", key: "email" },
    { label: "Saldo", key: "saldo" },
    { label: "Afiliado", key: "afiliado" },
];
const rows = [
    {
        name: "John Doe",
        email: "email@teste.com",
        saldo: 100,
        afiliado: true,
    },
    {
        name: "John Doe",
        email: "email@teste.com",
        saldo: 100,
        afiliado: true,
    },{
        name: "John Doe",
        email: "email@teste.com",
        saldo: 100,
        afiliado: true,
    },{
        name: "John Doe",
        email: "email@teste.com",
        saldo: 100,
        afiliado: true,
    },{
        name: "John Doe",
        email: "email@teste.com",
        saldo: 100,
        afiliado: true,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="flex justify-between">

            <div class="text-4xl text-white font-bold mb-5">Dashboard</div>
            <div class="flex gap-x-5 -mt-4">

                <TextBox label="Online" value="10.000" label-text="text-green-500" >
                    <template #icon>
                        <UserIcon class="w-5 fill-green-500" />
                    </template>
                </TextBox>
                <TextBox label="Cadastros" value="10.000" >
                    <template #icon>
                        <UserIcon class="w-5 " />
                    </template>
                </TextBox>
            </div>
            
        </div>
        <div class="h-64 bg-black text-white text-center">Gráfico</div>

        <div class="grid grid-cols-5 gap-x-2 mt-4">
            <CurrencyBox label="Lucro em 30 dias" value="R$ 30.000,00" />
            <CurrencyBox label="Prejuizo em 30 dias" value="R$ 30.000,00" negative/>
            <CurrencyBox label="Lucro Total" value="R$ 30.000,00" />
            <CurrencyBox label="Prejuizo Total" value="R$ 30.000,00" negative/>
            <CurrencyBox label="Lucro do dia" value="R$ 30.000,00" />

        </div>
        <div class="text-2xl font-bold text-white mt-6 mb-2">
            Últimos Cadastros
        </div>
        <BaseTable  class="table-xs" :columns="columns" :rows="rows">
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
            <template #saldo="{ value }">
               <td>
                {{ value.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}) }}
               </td>
            </template>
            <template #afiliado="{ value }">
                <td>
                    <div
                        v-if="value"
                    >
                        SIM
                    </div>
                    <div
                        v-else
                    >
                        NÃO
                    </div>
                </td>
            </template>
        </BaseTable>
        <BaseModal v-model="showModal" title="Gerenciar Afiliado">
            teste
        </BaseModal>
    </AuthenticatedLayout>
</template>
