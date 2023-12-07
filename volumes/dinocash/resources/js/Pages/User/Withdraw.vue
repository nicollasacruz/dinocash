<template>
    <UserLayouyt>
        <div class="p-2 lg:px-8">
            <div
                class="text-center uppercase text-xl lg:text-3xl text-gray-800 mb-4"
            >
                Sacar
            </div>
            <div class="w-full text-center flex-col flex gap-y-4 text-gray-800">
                <input
                    type="number"
                    class="bg-white mx-auto max-w-xs border-8 rounded-xl border-gray-800 w-full"
                    placeholder="Digite o valor da aposta"
                    v-model="amount"
                />
                <img :src="pixLogo" class="mx-auto mb-5 w-32 max-w-sm" alt="" />
                <button
                    @click="withdraw"
                    class="mx-auto py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                >
                    Sacar
                </button>
                <div class="mt-10 text-lg">
                    <div>Saldo disponível:</div>
                    <div>{{ toBRL(100) }}</div>
                </div>
                <div class="mt-10">
                    Saques serão enviados em até 24 horas úteis após a retirada.
                </div>
            </div>
        </div>
        <Loading :loading="loading" />
    </UserLayouyt>
</template>
<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import dayjs from "dayjs";
import { ref } from "vue";
import pixLogo from "../../../../storage/imgs/user/pix_logo.svg";
import axios from "axios";
import Loading from "../../Components/Loading.vue";
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const loading = ref(false);
const amount = ref(0);

async function withdraw() {
    loading.value = true;
    try {
        const { data } = await axios.post("/user/saque", {
            amount: amount.value,
        });
        toast.success("Saque pendente de aprovação!");
    } catch (error) {
        alert(error.response.data.message);
    } finally {
        loading.value = false;
        amount.value = 0;
    }
}

function toBRL(value) {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(value);
}
</script>
