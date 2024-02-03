<template>
    <Head title="Movimentações" />
    <UserLayout>
        <div class="px-2 md:px-12 flex flex-col h-full">
            <div
                class="font-menu text-5xl mb-4 font-extrabold px-4 mt-6 text-verde"
            >
                Movimentação
            </div>
            <div
                class="grid grid-cols-4 text-[11px] lg:text-[1rem] px-5 font-thin"
            >
                <div>Data</div>
                <div class="text-center">Movimentação</div>
                <div class="text-center">Status</div>
                <div class="text-end pr-6">Valor</div>
            </div>
            <div class="overflow-auto flex flex-col gap-y-2">
                <div
                    v-for="(
                        { updated_at, transaction_type, type, amount }, i
                    ) in transactions"
                    class="grid grid-cols-4  py-3 text-[11px] sm:text-sm lg:text-lg text-center border border-roxo-fundo rounded-xl p-3 px-5 md:text-[1rem]"
                >
                    <div class="text-left">
                        {{ dayjs(updated_at).format("DD/MM/YYYY HH:mm:ss") }}
                    </div>
                    <div class="text-center">
                        {{
                            transaction_type === "deposit"
                                ? "DEPÓSITO"
                                : "SAQUE"
                        }}
                    </div>
                    <div
                        class="text-center ml-4 font-bold"
                        :class="
                            type === 'pending'
                                ? 'text-yellow-500'
                                : type === 'rejected'
                                ? 'text-red-500'
                                : 'text-green-500'
                        "
                    >
                        {{
                            type === "pending"
                                ? "PENDENTE"
                                : type === "rejected"
                                ? "REJEITADO"
                                : "FINALIZADO"
                        }}
                    </div>
                    <div class="text-end">
                        {{ toBRL(amount) }}
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
<script setup lang="ts">
import UserLayout from "../..//Layouts/UserLayout.vue";
import dayjs from "dayjs";

const { transactions } = defineProps(["transactions"]);
// const transactions = [];
function toBRL(value) {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(value);
}
</script>

<style scoped>
.overflow-x-auto {
    scrollbar-width: thin; /* Para navegadores que suportam a propriedade scrollbar-width */
    scrollbar-color: #555555 #212121; /* Cor da barra de rolagem e cor do fundo da barra de rolagem */
}

.overflow-x-auto::-webkit-scrollbar {
    width: 8px; /* Largura da barra de rolagem no Chrome/Safari/Edge */
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background-color: #555555; /* Cor da barra de rolagem */
    border-radius: 4px; /* Borda arredondada da barra de rolagem */
}

.overflow-x-auto::-webkit-scrollbar-track {
    background-color: #212121; /* Cor do fundo da barra de rolagem */
}
</style>
