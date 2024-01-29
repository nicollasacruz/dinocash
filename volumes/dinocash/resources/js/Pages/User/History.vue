<template>
    <Head title="Games" />
    <UserLayouyt>
        <div class="px-2 md:px-12 flex flex-col h-full">
            <div
                class="font-menu text-5xl mb-4 font-extrabold px-4 mt-6 text-verde-escuro"
            >
                Histórico
            </div>
            <div
                class="grid grid-cols-4 text-[11px] lg:text-[1rem] px-5 font-thin"
            >
                <div>Data</div>
                <div class="text-center">Distancia</div>
                <div class="text-center">Resultado</div>
                <div class="text-end pr-6">Valor</div>
            </div>
            <div class="overflow-auto flex flex-col gap-y-2">
                <div
                    v-for="(
                        { updated_at, distance, type, finalAmount }, i
                    ) in listTransactions"
                    class="grid grid-cols-4 text-[11px] sm:text-sm lg:text-lg border border-roxo-fundo rounded-xl p-3 px-5 md:text-[1rem]"
                >
                    <div>
                        {{ dayjs(updated_at).format("DD/MM/YYYY HH:mm:ss") }}
                    </div>
                    <div class="text-center">{{ distance }} M</div>
                    <div
                        class="text-center ml-4 font-bold"
                        :class="
                            type === 'win'
                                ? 'text-verde-escuro'
                                : 'text-red-500'
                        "
                    >
                        {{ type === "win" ? "GANHOU" : "PERDEU" }}
                    </div>
                    <div class="text-end">
                        {{ toBRL(finalAmount) }}
                    </div>
                </div>
            </div>
        </div>
    </UserLayouyt>
</template>
<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import dayjs from "dayjs";

const { transactions } = defineProps(["transactions"]);
const listTransactions = transactions.reverse();
function toBRL(value) {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(value);
}
</script>
