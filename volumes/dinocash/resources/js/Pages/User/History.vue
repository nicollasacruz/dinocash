<template>
    <UserLayouyt>
        <div class="p-3 px-8">
            <div class="text-center uppercase text-3xl text-gray-800 mb-4">
                Histórico de jogadas
            </div>
            <div class="w-full flex-col flex gap-y-4">
                <div class="grid grid-cols-4">
                    <div class="text-center text-xl text-gray-800">Data</div>
                    <div class="text-center text-xl text-gray-800">
                        Distancia
                    </div>
                    <div class="text-center text-xl text-gray-800">
                        Resultado
                    </div>
                    <div class="text-center text-xl text-gray-800">Valor</div>
                </div>
                <div v-for="{date, distance, result, amount},i in data" class="grid grid-cols-4 bg-gray-200 py-2">
                    <div class="text-center text-xl text-gray-800">
                        {{ dayjs(date).format("DD/MM/YYYY") }}
                    </div>
                    <div class="text-center text-xl text-gray-800">
                        {{ distance }}
                    </div>
                    <div class="text-center text-xl text-gray-800">
                        {{ result }}
                    </div>
                    <div class="text-center text-xl text-gray-800">
                        {{ toBRL(amount) }}
                    </div>
                </div>
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
