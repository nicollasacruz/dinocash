<template>
    <UserLayouyt>
        <div class="p-2 lg:px-8">
            <div
                class="text-center uppercase text-xl lg:text-3xl text-gray-800 mb-4"
            >
                Depósito
            </div>
            <div class="w-full flex-col flex gap-y-4 text-gray-800">
                <input
                    type="number"
                    class="bg-white mx-auto max-w-xs border-8 rounded-xl border-gray-800 w-full"
                    placeholder="Digite o valor da aposta"
                    v-model="amount"
                />
                <img :src="pixLogo" class="mx-auto mb-5 w-32 max-w-sm" alt="" />
                <button
                    @click="depositHandle()"
                    class="mx-auto py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                >
                    Depositar
                </button>
            </div>
        </div>
    </UserLayouyt>
</template>
<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import dayjs from "dayjs";
import { ref } from "vue";
import pixLogo from "../../../../storage/imgs/user/pix_logo.svg";
import axios from "axios";

const amount = ref(0);

async function depositHandle() {
    try {
        const response = await axios.post(route("user.deposito.store"), {
            amount: amount.value,
        });
        const result = response.data;
        console.log(result);

        return result;
    } catch (error) {
        console.error("Erro na pesquisa:", error);

        throw error;
    }
}

function toBRL(value) {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(value);
}
</script>
