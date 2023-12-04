<template>
    <UserLayouyt>
        <div class="p-2 lg:px-8">
            <div class="text-center uppercase text-xl lg:text-3xl text-gray-800 mb-4">
                Saque / Depósito
            </div>
            <div class="w-full flex-col flex gap-y-4 text-gray-800">
               
            </div>
        </div>
    </UserLayouyt>
</template>
<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import dayjs from "dayjs";
const data = [
    {
        id: 1,
        date: "2021-09-01",
        distance: 100,
        result: "Ganhou",
        amount: 100,
    },
    {
        id: 2,
        date: "2021-09-01",
        distance: 100,
        result: "Ganhou",
        amount: 100,
    },
    {
        id: 3,
        date: "2021-09-01",
        distance: 100,
        result: "Ganhou",
        amount: 100,
    },
    {
        id: 4,
        date: "2021-09-01",
        distance: 100,
        result: "Ganhou",
        amount: 100,
    },
    {
        id: 5,
        date: "2021-09-01",
        distance: 100,
        result: "Ganhou",
        amount: 100,
    },
    {
        id: 6,
        date: "2021-09-01",
        distance: 100,
        result: "Ganhou",
        amount: 100,
    },
];
function toBRL(value) {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(value);
}
</script>
